<?php
/**
 * +----------------------------------------------------------------------
 * | wmt [ 七牛云存储驱动 ]
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015~2019 http://www.wmt.ltd All rights reserved.
 * +----------------------------------------------------------------------
 * | 版权所有：贵州鸿宇叁柒柒科技有限公司
 * +----------------------------------------------------------------------
 * | Author: shadow <admin@hongyuvip.com>  QQ: 1527200768
 * +----------------------------------------------------------------------
 * | Version: v1.0.0  Date:2019-05-21 Time:15:26
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelStorage\Adapters;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class QiniuAdapter extends AbstractAdapter
{

    protected $uploadManager;
    protected $bucketManager;
    private $accessKey;
    private $accessSecret;
    private $bucketName;
    private $token;

    public function __construct($prefix = '')
    {
        // TODO: 开发七牛云文件系统驱动
        // 开发参考:
        // https://laravelacademy.org/post/9486.html
        // https://laravelacademy.org/post/3850.html
        // vendor/overtrue/flysystem-qiniu/src/QiniuAdapter.php
        // https://github.com/zgldh/qiniu-laravel-storage

        $this->uploadManager = new UploadManager();
        $this->accessKey = \config('filesystems.disks.qiniu.key');
        $this->accessSecret = \config('filesystems.disks.qiniu.secret');
        $this->bucketName = \config('filesystems.disks.qiniu.bucket');
        $auth = new Auth($this->accessKey, $this->accessSecret);
        $this->bucketManager = new BucketManager($auth);
        $this->token = $auth->uploadToken($this->bucketName);
        $this->setPathPrefix($prefix);
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        return $this->upload($path, $contents);
    }

    /**
     * Write a new file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->upload($path, $resource, true);
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        return $this->upload($path, $contents);
    }

    /**
     * Update a file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->upload($path, $resource, true);
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        $path = $this->applyPathPrefix($path);
        $newpath = $this->applyPathPrefix($newpath);
        $error = $this->bucketManager->rename($this->bucketName, $path, $newpath);
        return $error == null ? true : false;
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        $path = $this->applyPathPrefix($path);
        $newpath = $this->applyPathPrefix($newpath);
        $error = $this->bucketManager->copy($this->bucketName, $path, $this->bucketName, $newpath);
        return $error == null ? true : false;
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        $this->applyPathPrefix($path);
        $error = $this->bucketManager->delete($this->bucketName, $path);
        return $error == null ? true : false;
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        $path = $this->applyPathPrefix($path);
        $stat = $this->bucketManager->stat($this->bucketName, $path);
        if ($stat[0] == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        $path = $this->applyPathPrefix($path);
        list($fileInfo, $error) = $this->bucketManager->stat($this->bucketName, $path);
        if ($fileInfo) {
            return $fileInfo;
        } else {
            throw new FileNotFoundException('对应文件不存在');
        }
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->bucketManager->listFiles($this->bucketName);
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        return $this->read($path);
    }

    /**
     * Get the size of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        $fileInfo = $this->read($path);
        return $fileInfo['fsize'];
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        $fileInfo = $this->read($path);
        return $fileInfo['fileType'];
    }

    /**
     * Get the last modified time of a file as a timestamp.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        $fileInfo = $this->read($path);
        return $fileInfo['putTime'];
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    protected function upload(string $path, $contents, $stream = false)
    {
        $path = $this->applyPathPrefix($path);
        try {
            if ($stream) {
                $response = $this->uploadManager->put($this->token, $path, $contents);
            } else {
                $response = $this->uploadManager->putFile($this->token, $path, $contents);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
        list($uploadResult, $error) = $response;
        if ($uploadResult) {
            return $uploadResult;
        } else {
            throw new UploadException('上传文件到七牛失败：' . $error->message());
        }
    }
}