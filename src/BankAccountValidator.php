<?php

namespace SwedishBankAccountValidator;

class BankAccountValidator
{
    /**
     * @param string $clearingNumberString
     * @param string $serialNumberString
     * @return ValidatorResult
     */
    public static function withAccount($clearingNumberString, $serialNumberString = null)
    {
        $validatorResult = ClearingNumber::validate($clearingNumberString);
        if ($validatorResult->hasError()) {
            return $validatorResult;
        }
        $clearingNumber = new ClearingNumber($clearingNumberString);
        $bank = Bank::requireInstanceByClearingNumber($clearingNumber);
        return $bank->validateSerialNumber($serialNumberString);
    }
}
