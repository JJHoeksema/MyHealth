<?php
namespace Auth;


use DMF\Events\StatusChangeEvent;

class Permission {
    /**
     * @var \DMF\App;
     */
    protected $app;
    protected $minViewLevel;
    protected $maxViewLevel;
    protected $minEditLevel;
    protected $maxEditLevel;
    protected $requiresLogin;

    public function __construct($app, $minLevel = -1, $maxLevel = NULL, $options = []) {
        $this->app = $app;
        $this->requiresLogin    = ($minLevel < 0) ? false : true;
        $this->minViewLevel     = $minLevel;
        $this->maxViewLevel     = $maxLevel;
        $this->minEditLevel     = $minLevel;
        $this->maxEditLevel     = $maxLevel;
        foreach($options as $option => $value){
            $this->setOption($option, $value);
        }
    }

    public function setOption($option, $value){
        switch($option){
            case "min-view":
                $this->minViewLevel = intval($value);
                break;
            case "max-view":
                $this->maxViewLevel = ($value == NULL)? NULL : intval($value);
                break;
            case "min-edit":
                $this->minEditLevel = intval($value);
                break;
            case "max-edit":
                $this->maxEditLevel = ($value == NULL)? NULL : intval($value);
                break;
            default:
                throw new \Exception("Invalid Option '$option'");
                break;
        }
    }

    public function canView(Account $account, $error = true){
        if($this->requiresLogin && !$account->isLoggedIn()) {
            if($error) $this->app->activateEvent(new StatusChangeEvent(401, "login required"));
            return false;
        }

        if (    $this->minViewLevel > $account->getAccessLevel()
            || ($this->maxViewLevel != NULL && $account->getAccessLevel() > $this->maxViewLevel)
        ) {
            if($error) $this->app->activateEvent(new StatusChangeEvent(404, "User isn't qualified to view"));
            return false;
        }

        $account->refresh();
        return true;
    }

    public function canEdit(Account $account, $error = true){
        if($this->requiresLogin && !$account->isLoggedIn()){
            if($error) $this->app->activateEvent(new StatusChangeEvent(401, "login required"));
            return false;
        }

        if (    $this->minEditLevel > $account->getAccessLevel()
            || ($this->maxEditLevel != NULL && $account->getAccessLevel() > $this->maxEditLevel)
        ) {
            if($error) $this->app->activateEvent(new StatusChangeEvent(400, "Account isn't qualified to edit"));
            return false;
        }

        $account->refresh();
        return true;
    }


}