<?php


/**
 * User data model on client side.
 */
class User extends dbMappedObject{
    /** @var int */
    public $id = Null;

    /** @var int */
    public $sessionId = Null;

    /** @var string */
    public $sessionType = Null;

    /** @var array */
    public $details = array();

    /**
     * Construct the user object based on database information.
     * @param int $userId
     * @param int $sessionId
     */
    public function __construct(){

        /** @var string
        * the object table in SQL DB */
        $this->table = 'User';
    }
}
?>
