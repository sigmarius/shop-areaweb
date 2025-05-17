<?php

namespace App\Exceptions\Product;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProductNotFoundException extends Exception
{
    public function __construct(
        string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct(
            $message ?? __('errors.product.not_found'),
                Response::HTTP_NOT_FOUND,
            $previous
        );
    }

    /**
     * Этот метод используется для журналирования
     * если в методе ничего не содержится, запись в лог добавлена не будет
     * все, что добавлено здесь, будет заменять дефолтную запись в лог от Laravel
     * или добавлять запись в дефолтный лог
     *
     * @return void
     */
    public function report(): void
    {
        logger()->debug('ProductNotFoundException', [$this->getFile()]);
    }

    /**
     * Этот метод будет использоваться в файле bootstrap/app.php
     * все что записано здесь, автоматически будет вызываться в методе withExceptions()
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        return responseFailed(
            $this->getMessage(),
            'Product is draft',
            $this->getCode()
        );
    }
}
