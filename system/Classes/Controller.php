<?php namespace CodeIgniter;

use CodeIgniter\CI;

class Controller {

    /**
     * CodeIgniter DI Container/Registry instance
     *
     * @var
     */
    protected $ci;

    //--------------------------------------------------------------------

    public function __construct( $ci )
    {
        if (! $ci instanceof CI)
        {
            throw new \HttpInvalidParamException('You must pass an instance of \CodeIgniter\CI as the first parameter of a controller.');
        }

        $this->ci = $ci;
    }

    //--------------------------------------------------------------------


}