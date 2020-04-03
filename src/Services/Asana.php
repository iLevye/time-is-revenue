<?php


namespace App\Services;


use Asana\Client;

class Asana
{
    private $accessToken;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    function getWorkspaces(){
        $client = Client::accessToken($this->accessToken, array('headers' => array('asana-disable' => 'string_ids')));
        $me = $client->users->me();
        return $me->workspaces;
    }

    function getProject($projectId){
        $client = Client::accessToken($this->accessToken, array('headers' => array('asana-disable' => 'string_ids,new_sections')));
        return $client->projects->findById($projectId);
    }

    function getProjects($workspaceId){
        $client = Client::accessToken($this->accessToken, array('headers' => array('asana-disable' => 'string_ids')));

        $projects = $client->projects->findByWorkspace($workspaceId);
        return $projects;
    }

    function getTasks($projectId){
        $client = Client::accessToken($this->accessToken, array('headers' => array('asana-disable' => 'string_ids')));
        $tasks = $client->tasks->findByProject($projectId);
        return $tasks;
    }

    function searchTasks($workspaceId, $project, $assignedToMe = false){
        $client = Client::accessToken($this->accessToken, array('headers' => array('asana-disable' => 'string_ids')));
        $configs = [
            'project' => $project
        ];
        if($assignedToMe){
            $configs['assignee'] = $client->users->me()->id;
        }

        return $client->tasks->search($workspaceId, $configs);
    }
}