<?php

namespace Web3php\Chain\Ethereum\Subscription\Response;

use Web3php\Exception\SubscribeException;

class ResponseResult
{
    protected mixed $result = null;

    protected string $subscription = "";

    protected int $id = 0;

    public function __construct(protected string $response)
    {
        $result = json_decode($response);
        if (isset($result->error)) {
            throw new SubscribeException($result->error->message, $result->error->code);
        }
        if (isset($result->params)) {
            if (isset($result->params->subscription)) {
                $this->subscription = $result->params->subscription;
            }
            if (isset($result->params->result)) {
                $this->result = $result->params->result;
            }
        }
        if (isset($result->result)) {
            $this->subscription = $result->result;
        }
        if (isset($result->id)) {
            $this->id = $result->id;
        }
    }

    public function getSubscription(): string
    {
        return $this->subscription;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function getId(): int
    {
        return $this->id;
    }

}