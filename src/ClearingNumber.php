<?php
namespace SwedishBankAccountValidator;

use LogicException;

class ClearingNumber
{
    /** @var string */
    private $clearingNumber;

    /**
     * ClearingNumber constructor.
     * @param string $clearingNumberString
     */
    public function __construct($clearingNumberString)
    {
        $this->clearingNumber = substr($clearingNumberString, 0, 4);
        if (!self::validate(substr($clearingNumberString, 0, 4))) {
            throw new LogicException("The clearing number is invalid");
        }
    }

    /**
     * @param string $clearingNumberString
     * @return ValidatorResult
     */
    public static function validate($clearingNumberString)
    {
        $clearingNumberString = substr($clearingNumberString, 0, 4);
        $validatorResult = new ValidatorResult();
        if (!is_numeric($clearingNumberString)) {
            $validatorResult
                ->setInvalidClearingNumber()
                ->setSwedishErrorMessage("Clearingnumret är inte numeriskt: '$clearingNumberString'")
                ->setEnglishErrorMessage("The clearing-number is not numeric: '$clearingNumberString'");
        } elseif (!ClearingNumberRange::getInstance()->isSupportedClearingNumber($clearingNumberString)) {
            $validatorResult
                ->setInvalidClearingNumber()
                ->setSwedishErrorMessage("Clearingnumret stöds ej: '$clearingNumberString'")
                ->setEnglishErrorMessage("Unsupported clearing-number: '$clearingNumberString'");
        }
        return $validatorResult;
    }

    public function __toString()
    {
        return $this->clearingNumber;
    }
}
