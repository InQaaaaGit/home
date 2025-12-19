<?php
namespace local_cdo_unti2035bas\infrastructure\mediainfo;

use local_cdo_unti2035bas\exceptions\exec_error;


class mediainfo_service {
    private string $mediainfocmd;

    public function __construct(string $mediainfocmd) {
        $this->mediainfocmd = $mediainfocmd;
    }

    public function get_version(): string {
        exec("{$this->mediainfocmd} --Version", $out, $ret);
        if ($ret != 0) {
            throw new exec_error('Mediainfo exec error', $ret);
        }
        return join("\n", $out);
    }

    public function get_duration(string $filepath): int {
        $cmd = escapeshellcmd($this->mediainfocmd);
        $args = '--output=JSON ' . escapeshellarg($filepath);
        $out = '';
        exec("{$cmd} {$args}", $out, $ret);
        if ($ret != 0) {
            throw new exec_error('Mediainfo exec error', $ret);
        }
        
        $output = join('', $out);
        if (empty($output)) {
            throw new exec_error('Mediainfo returned empty output', $ret);
        }
        
        $decoded = json_decode($output, true);
        if ($decoded === null) {
            throw new exec_error('Mediainfo returned invalid JSON: ' . $output, $ret);
        }
        
        $info = mediainfo_schema::validate($decoded);
        $infogeneral = array_filter($info->media->track, fn($t) => $t->type == 'General')[0];
        return (int)$infogeneral->duration;
    }
}
