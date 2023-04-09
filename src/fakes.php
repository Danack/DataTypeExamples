<?php


interface Request {}

class JsonResponse {

    protected function __construct(private $data)
    {
    }

    public static function create($data)
    {
        return new self($data);
    }
}

class UserStorage {

    public function createUser(UserSignupParameters $userSignupParameters)
    {
        $result = var_export($userSignupParameters, true);
        $result['user_id'] = 12345;

        return $result;
    }
}