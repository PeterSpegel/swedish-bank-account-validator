<?php
namespace SwedishBankAccountValidator;

class Bank
{
    const AMFA_BANK_AB = 'Amfa Bank AB';
    const BNP_PARIBAS_FORTIS_BANK = 'BNP Paribas Fortis Bank';
    const CITIBANK = 'Citibank';
    const DANSKE_BANK = 'Danske Bank';
    const DNB_BANK = 'DNB Bank';
    const EKOBANKEN = 'Ekobanken';
    const FOREX_BANK = 'Forex Bank';
    const HANDELSBANKEN = 'Handelsbanken';
    const ICA_BANKEN_AB = 'ICA Banken AB';
    const IKANO_BANK = 'IKANO Bank';
    const LANSFÖRSÄKRINGAR_BANK = 'Länsförsäkringar Bank';
    const MARGINALEN_BANK = 'Marginalen Bank';
    const NORDAX_BANK_AB = 'Nordax Bank AB';
    const NORDEA = 'Nordea';
    const NORDEA_PLUSGIRO = 'Nordea/Plusgirot';
    const NORDEA_PERSON_ACCOUNT = 'Nordea - personkonto';
    const NORDNET_BANK = 'Nordnet Bank';
    const RESURS_BANK = 'Resurs Bank';
    const RIKSGALDEN = 'Riksgälden';
    const ROYAL_BANK_OF_SCOTLAND = 'Royal bank of Scotland';
    const SBAB = 'SBAB';
    const SEB = 'SEB';
    const SKANDIABANKEN = 'Skandiabanken';
    const SPARBANKEN_SYD = 'Sparbanken Syd';
    const SWEDBANK = 'Swedbank';
    const SWEDBANK_SPARBANKEN_ORESUND = 'Swedbank (f.d. Sparbanken Öresund)';
    const ALANDSBANKEN_SVERIGE_AB = 'Ålandsbanken Sverige AB';

    /** @var string */
    private $bankName;
    /** @var string */
    private $accountType;
    /** @var ClearingNumber */
    private $clearingNumber;

    /**
     * Bank constructor.
     * @param string $bankName
     * @param string $accountType
     * @param ClearingNumber $clearingNumber
     */
    private function __construct($bankName, $accountType, ClearingNumber $clearingNumber)
    {
        $this->bankName = $bankName;
        $this->accountType = $accountType;
        $this->clearingNumber = $clearingNumber;
    }

    public static function requireInstanceByClearingNumber(ClearingNumber $clearingNumber)
    {
        $range = ClearingNumberRange::getInstance()->requireBankByClearingNumber($clearingNumber);

        return new self(
            $range['bankName'],
            $range['accountType'],
            $clearingNumber
        );
    }

    /**
     * @param string $serialNumber
     * @return ValidatorResult
     */
    public function validateSerialNumber($serialNumber)
    {
        $validatorResult = new ValidatorResult($this->getBankName(), $this->getClearingNumber(), $serialNumber);
        if (in_array($this->accountType, [
            ClearingNumberRange::ACCOUNT_TYPE_1_1,
            ClearingNumberRange::ACCOUNT_TYPE_1_2])
        ) {
            $this->validateType1SerialNumber($validatorResult, $serialNumber);
            $number = $this->accountType == ClearingNumberRange::ACCOUNT_TYPE_1_1 ?
                substr($serialNumber, 1) : $serialNumber;
            $this->validateChecksum($validatorResult, 11, $number);
        } elseif ($this->accountType == ClearingNumberRange::ACCOUNT_TYPE_2_1) {
            $this->validateType21SerialNumber($validatorResult, $serialNumber);
            $this->validateChecksum($validatorResult, 10, $serialNumber);
        } elseif ($this->accountType == ClearingNumberRange::ACCOUNT_TYPE_2_2) {
            $this->validateType22SerialNumber($validatorResult, $serialNumber);
            $this->validateChecksum($validatorResult, 11, $serialNumber);
        } elseif ($this->accountType == ClearingNumberRange::ACCOUNT_TYPE_2_3) {
            $this->validateType23SerialNumber($validatorResult, $serialNumber);
            $this->validateChecksum($validatorResult, 10, substr($serialNumber, -10));
        }
        return $validatorResult;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @return ClearingNumber
     */
    public function getClearingNumber()
    {
        return $this->clearingNumber;
    }

    /**
     * @param ValidatorResult $validatorResult
     * @param string $serialNumber
     */
    private function validateType1SerialNumber(
        ValidatorResult $validatorResult,
        $serialNumber
    ) {
        if ($validatorResult->hasError()) {
            return;
        }
        if (!preg_match('/^\d{11}$/', $serialNumber)) {
            $validatorResult
                ->setInvalidSerialNumberFormat()
                ->setSwedishErrorMessage("Clearingnumret och kontonumret måste vara exakt 11 siffror: '$serialNumber'")
                ->setEnglishErrorMessage(
                    "Clearing-number and serial-number should be exactly 11 digits: '$serialNumber'"
                );
        }
    }

    /**
     * @param ValidatorResult $validatorResult
     * @param string $serialNumber
     */
    private function validateType21SerialNumber(ValidatorResult $validatorResult, $serialNumber)
    {
        if ($validatorResult->hasError()) {
            return;
        }
        if (!preg_match('/^\d{10}$/', $serialNumber)) {
            $validatorResult
                ->setInvalidSerialNumberFormat()
                ->setSwedishErrorMessage("Kontonumret måste vara exakt 10 siffror: '$serialNumber'")
                ->setEnglishErrorMessage("Serial-number should be exactly 10 digits: '$serialNumber'");
        }
    }

    /**
     * @param ValidatorResult $validatorResult
     * @param string $serialNumber
     */
    private function validateType22SerialNumber(ValidatorResult $validatorResult, $serialNumber)
    {
        if ($validatorResult->hasError()) {
            return;
        }
        if (!preg_match('/^\d{9}$/', $serialNumber)) {
            $validatorResult
                ->setInvalidSerialNumberFormat()
                ->setSwedishErrorMessage("Kontonumret måste vara exakt 9 siffror: '$serialNumber'")
                ->setEnglishErrorMessage("Serial-number should be exactly 9 digits: '$serialNumber'");
        }
    }

    /**
     * @param ValidatorResult $validatorResult
     * @param string $serialNumber
     */
    private function validateType23SerialNumber(ValidatorResult $validatorResult, $serialNumber)
    {
        if ($validatorResult->hasError()) {
            return;
        }
        if (!preg_match('/^\d{1,10}$/', $serialNumber)) {
            $validatorResult
                ->setInvalidSerialNumberFormat()
                ->setSwedishErrorMessage("Kontonumret får inte vara längre än 10 siffror: '$serialNumber'")
                ->setEnglishErrorMessage("Serial-number should be no longer than 10 digits: '$serialNumber'");
        }
    }

    private function validateChecksum(ValidatorResult $validatorResult, $modulus, $number)
    {
        if ($validatorResult->hasError()) {
            return;
        }
        if ($modulus == 10 && ModulusCalculator::verifyMod10Checksum($number)) {
            return;
        }
        if ($modulus == 11 && ModulusCalculator::verifyMod11Checksum($number)) {
            return;
        }

        if ($this->bankName == Bank::SWEDBANK) {
            $validatorResult
                ->setIsInvalidSwedbankChecksum()
                ->setSwedishErrorMessage(
                    "Ogiltig checksumma för konto: $number" . PHP_EOL .
                    "I sällsynta fall kan dock Swedbanks kontonummer ha en dålig checksumma."
                )
                ->setEnglishErrorMessage(
                    "Incorrect checksum for number: $number" . PHP_EOL .
                    "However, in rare cases Swedbank account number with bad checksum do exists."
                );
            return;
        }

        $validatorResult
            ->setInvalidChecksum()
            ->setSwedishErrorMessage("Ogiltig checksumma för konto: $number")
            ->setEnglishErrorMessage("Incorrect checksum for number: $number");
    }
}
