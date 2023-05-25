<?php

namespace Web3php\Constants\Errors\ChainErrors;

class ErrorCode
{
    public const SENDER_NOT_IMPLEMENTED = "Sender Not Implemented";

    public const NOT_THE_SAME_FROM_ADDRESS = "Not the same from address";

    public const TRANSACTION_BEING_PACKAGED = "Transaction is being packaged";

    public const TRANSACTION_FAILED = "Transaction Failed";

    public const TRANSACTION_FAILED_CODE = 10001;

    public const TRANSACTION_BEING_PACKAGED_CODE = 10002;

    public const NOT_FOUND_CONTRACT_FUNCTION = "Not Found Contract Function";

    public const UNINITIALIZED_EVENT = "Uninitialized Event";
}