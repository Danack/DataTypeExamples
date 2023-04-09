<?php

use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\HasInputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;
use DataType\GetInputTypesFromAttributes;
use DataType\DataType;
use DataType\ExtractRule\GetArrayOfString;
use DataTypeTest\Integration\PasswordDoubleCheck;
use DataType\ExtractRule\GetOptionalString;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\Create\CreateFromArray;
use DataType\ProcessRule\MinimumCount;
use DataType\ProcessRule\IsEmail;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/fakes.php";


/**
 * A class that represents the data for a 'user signup' endpoint.
 */
class UserSignupParameters implements DataType
{
    use CreateFromArray;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[UserNames('names')]
        public array $names,

        #[Nickname('nickname')]
        public string $nickname,

        #[EmailAddress('email_address')]
        public string $email,
    ) {
    }
}

/**
 * A pretend controller to process the request.
 */
class UserSignupController
{
    public function do_the_needful(
        /*Request*/ array $request,
        UserStorage $userStorage
    ): JsonResponse {

        // Just using an array instead of a proper request for the example.
        $userSignupParameters = UserSignupParameters::createFromArray($request);

        // In real code, you'd probably be creating types from a request.
        //$userSignupParameters = UserSignupParameters::createFromRequest($request);
        $data = $userStorage->createUser($userSignupParameters);

        return JsonResponse::create($data);
    }
}


/**
 * A 'name' type. People can have multiple names.
 */
#[\Attribute]
class Name implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetOptionalString(),
            new MinLength(2),
            new MaxLength(200)
        );
    }
}

/**
 * Users can have multiple names...
 */
#[\Attribute]
class UserNames implements HasInputType
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetArrayOfString(),
            new MinimumCount(1)
        );
    }
}

/**
 * Maybe they can have a nickname/handle also.
 */
#[\Attribute]
class Nickname implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault('noob'),
        );
    }
}

/**
 * And they can have an email why not.
 */
#[\Attribute]
class EmailAddress implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new IsEmail()
        );
    }
}


$controller = new UserSignupController();

$request_data = [
    'email_address' => 'john@example.com',
    'names' => [
        'John',
        'Smith'
    ]
];

$response = $controller->do_the_needful(
    $request_data,
    new UserStorage
);

var_dump($response);