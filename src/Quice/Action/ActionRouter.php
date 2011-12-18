<?php

namespace Quice\Action;

class ActionRouter
{
    public $request = null;
    public $routers = array();

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * Example:  user/:username => 'user/profile'
     *
     * @param string $router Path used to match against this routing map
     * @return string|false An string of assigned values or a false on a mismatch
     */
    public function routing($path)
    {
        // Filter path
        if(!$router) {
            return false;
        }

        $routers = $this->routers;
        if (empty($routers)) {
            return false;
        } else {
            $routerNames = array_keys($routers);
        }

        // Finded in routers
        if(in_array($path, $routerNames)) {
            return $routers[$path];
        }

        // Matches path with parts defined by a map.
        $query = array();
        $matchedName = null;
        $routerParts = explode('/', $path);
        foreach($routerNames as $routerName) {
            $routerNameParts = explode('/', trim($routerName, '/'));
            if(count($routerNameParts) != count($routerParts)) {
                continue;
            }
            foreach ($routerParts as $key => $routerPart) {
                if($routerPart == '') {
                    continue;
                } else if(strpos($routerNameParts[$key],':') !== false) {
                    $query[substr($routerNameParts[$key], 1)] = $routerPart;
                } else if($routerNameParts[$key] != $routerPart) {
                    continue(2);
                }
            }
            $matchedName = $routers[$routerName];
        }

        // Not found
        if(!$matchedName) {
            return false;
        }

        // Set request query
        if($query) {
            foreach($query as $queryKey => $queryValue) {
                $this->request->setQuery($queryKey, $queryValue);
            }
        }

        return $matchedName;
    }
}