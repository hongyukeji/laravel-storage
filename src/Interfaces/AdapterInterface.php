<?php
/**
 * +----------------------------------------------------------------------
 * | wmt [ File Description ]
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015~2019 http://www.wmt.ltd All rights reserved.
 * +----------------------------------------------------------------------
 * | 版权所有：贵州鸿宇叁柒柒科技有限公司
 * +----------------------------------------------------------------------
 * | Author: shadow <admin@hongyuvip.com>  QQ: 1527200768
 * +----------------------------------------------------------------------
 * | Version: v1.0.0  Date:2019-05-25 Time:15:31
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelStorage\Interfaces;

use League\Flysystem\Config;

interface AdapterInterface
{
    public function write($path, $contents, Config $config);

    public function writeStream($path, $resource, Config $config);

    public function update($path, $contents, Config $config);

    public function updateStream($path, $resource, Config $config);

    public function rename($path, $newPath): bool;

    public function copy($path, $newpath): bool;

    public function delete($path): bool;

    public function deleteDir($dirname): bool;

    public function createDir($dirname, Config $config);

    public function has($path);

    public function read($path);

    public function readStream($path);

    public function listContents($directory = '', $recursive = false): array;

    public function getMetadata($path);

    public function getSize($path);

    public function getMimetype($path);

    public function getTimestamp($path);

    public function getTemporaryLink(string $path): string;

    public function getThumbnail(string $path, string $format = 'jpeg', string $size = 'w64h64');

    public function applyPathPrefix($path): string;

    public function getClient();

    public function upload(string $path, $contents, string $mode);

    public function normalizeResponse(array $response): array;
}