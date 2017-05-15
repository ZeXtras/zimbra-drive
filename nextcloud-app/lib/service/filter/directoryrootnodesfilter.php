<?php
/**
 * MIT License (MIT)
 *
 * Copyright (c) 2017 Zextras SRL
 */

namespace OCA\ZimbraDrive\Service\Filter;

use OCA\ZimbraDrive\Service\LogService;
use OCP\Files\Node;

class DirectoryRootNodesFilter implements NodesFilter
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var bool
     */
    private $isCaseSensitive;
    /**
     * @var LogService
     */
    private $logger;


    /**
     * DirectoryRootNodesFilter constructor.
     * @param $path string
     * @param $isCaseSensitive
     * @param LogService $logService
     */
    public function __construct($path, $isCaseSensitive, LogService $logService)
    {
        $this->path = $path;
        $this->isCaseSensitive = $isCaseSensitive;
        $this->logger = $logService;
    }

    /**
     * @param $nodes array of Node
     * @return array
     */
    public function filter($nodes)
    {
        $filteredNodes = array();
        /** @var Node $node */
        foreach($nodes as $node)
        {
            $nodeInternalPath = $node->getInternalPath();
            $nodeUserRootRelativePath = substr($nodeInternalPath, strlen("files"));
            if($this->isInTheDirectoryTree($nodeUserRootRelativePath, $this->path))
            {
                $filteredNodes[] = $node;
            }
        }
        return $filteredNodes;
    }

    /**
     * @param $path string
     * @param $treeDirectoryRoot string
     * @return bool
     */
    private function isInTheDirectoryTree($path, $treeDirectoryRoot)
    {
        $firstPathChar = substr($treeDirectoryRoot, 0, 1);
        if($firstPathChar !== "/")
        {
            $treeDirectoryRoot = "/" . $treeDirectoryRoot;
        }

        $lastPathChar = substr($treeDirectoryRoot, -1);
        if($lastPathChar !== "/")
        {
            $treeDirectoryRoot = $treeDirectoryRoot . "/";
        }

        if(strlen($path) <= strlen($treeDirectoryRoot))
        {
            return false;
        }
        $rootPath = substr($path, 0, strlen($treeDirectoryRoot));

        if(!$this->isCaseSensitive)
        {
            $rootPath = strtolower($rootPath);
            $treeDirectoryRoot = strtolower($treeDirectoryRoot);
        }

        if(strcmp($rootPath, $treeDirectoryRoot) === 0)
        {
            return true;
        }

        return false;
    }


}