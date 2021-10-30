<?php //phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols,Squiz.Functions.GlobalFunction.Found

declare(strict_types=1);

use Curl\Curl;

require_once __DIR__ . '/../vendor/autoload.php';

define('PUT_BASE_DIR', __DIR__ . '/../data/postalcode/jp');

$sources = [
    'https://www.post.japanpost.jp/zipcode/dl/oogaki/zip/ken_all.zip' => [ 'KEN_ALL.CSV', 2 ],
    'https://www.post.japanpost.jp/zipcode/dl/jigyosyo/zip/jigyosyo.zip' => [ 'JIGYOSYO.CSV', 7 ],
];

$data = [];
foreach ($sources as $url => $parseInfo) {
    $tmpData = parseCsv(
        downloadCsv($url, $parseInfo[0]),
        $parseInfo[1]
    );
    foreach ($tmpData as $zip1 => $list) {
        if (!isset($data[$zip1])) {
            $data[$zip1] = [];
        }
        $data[$zip1] = array_merge($data[$zip1], $list);
    }
}

foreach ($data as $zip1 => $list) {
    printf("save %03d ...\n", $zip1);
    usort(
        $list,
        function (string $lhs, string $rhs): int {
            return strnatcmp($lhs, $rhs);
        }
    );
    save(sprintf('%03d', $zip1), $list);
}

function downloadCsv(string $url, string $filename): string
{
    echo "Downloading $url ...\n";
    $curl = new Curl();
    $curl->get($url);
    if ($curl->error) {
        throw new Exception('Could not download ' . $url);
    }

    echo "Extracting $filename ...\n";
    $tmppath = tempnam(sys_get_temp_dir(), 'zip-');
    try {
        file_put_contents($tmppath, $curl->rawResponse);
        $zip = new ZipArchive();
        if ($zip->open($tmppath, 0) !== true) {
            throw new Exception('Could not open zip archive');
        }
        $csv = $zip->getFromName($filename);
        $zip->close();
        if ($csv === false) {
            throw new Exception('Could not extract ' . $filename . ' from archive');
        }
        @unlink($tmppath);
        return $csv;
    } catch (Throwable $e) {
        @unlink($tmppath);
        throw $e;
    }
}

/** @return array<int, string[]> */
function parseCsv(string $csv, int $pos): array
{
    $ret = [];

    echo "Parsing CSV...\n";
    $tmppath = tempnam(sys_get_temp_dir(), 'csv-');
    try {
        echo "  save tmp file...\n";
        file_put_contents($tmppath, mb_convert_encoding($csv, 'UTF-8', 'CP932'));

        echo "  parse...\n";
        $fh = fopen($tmppath, 'rt');
        while (!feof($fh)) {
            $line = fgetcsv($fh);
            if ($line === null || $line === false) {
                break;
            }
            if (preg_match('/^\d{7}$/', (string)$line[$pos])) {
                $zip1 = substr($line[$pos], 0, 3);
                $zip2 = substr($line[$pos], 3, 4);
                if (!isset($ret[$zip1])) {
                    $ret[$zip1] = [];
                }
                $ret[$zip1][] = $zip2;
            }
        }
        fclose($fh);
        @unlink($tmppath);
        return $ret;
    } catch (Throwable $e) {
        @unlink($tmppath);
        throw $e;
    }
}

/** @param string[] $zip2list */
function save(string $zip1, array $zip2list): void
{
    $filepath = PUT_BASE_DIR . '/' . $zip1 . '.json.gz';
    if (!file_exists(dirname($filepath))) {
        mkdir(dirname($filepath), 0755, true);
    }
    $json = json_encode($zip2list);
    file_put_contents($filepath, gzencode($json, 9, FORCE_GZIP));
}
