<?php
namespace local_cdo_unti2035bas;


class utils {
    public static function uuid4(): string {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * @param array<mixed> $src
     * @return array<mixed, mixed>
     */
    public static function groupby(array $src, callable $keyfunc) {
        $result = [];
        foreach ($src as $item) {
            $key = $keyfunc($item);
            $result[$key][] = $item;
        }
        return $result;
    }

    public static function str_to_bool(string $value): bool {
        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'y', 'да']);
    }

    public static function str_to_float(string $value): float {
        return (float)str_replace([',', ' '], ['.', ''], trim($value));
    }

    public static function mime_to_extension(string $mime): ?string {
        $ext = \core_filetypes::get_file_extension($mime);
        return is_string($ext) ? $ext : null;
    }
}
