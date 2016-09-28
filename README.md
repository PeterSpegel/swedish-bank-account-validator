# Swedish Bank Account Validator

Library for validating swedish bank accounts connected to the BankGiro-system by a combination of clearing number
and serial number.

The library validates that:
* Which bank an account number is connected to if that bank supports BankGiro
* If the serial number has the correct format and checksum (mod10 and mod11)

The logic of validating the accounts is based in the following document:

[Bankernas kontonummer](https://www.bankgirot.se/globalassets/dokument/anvandarmanualer/bankernaskontonummeruppbyggnad_anvandarmanual_sv.pdf) (Bankgirot)

[![Build Status](https://travis-ci.org/olanorlander/swedish-bank-account-validator.png)](https://travis-ci.org/olanorlander/swedish-bank-account-validator)

### Example
Validation of clearing number only. The result object contains a number of functions to query for specific errors.
It is also possible to get an error message in english or swedish.
```php
$result = BankAccountValidator::withAccount('9661');
if ($result->hasInvalidClearingNumber()) {
    echo "Wrong clearing-number:" . $result->getEnglishErrorMessage();
}
```

Validation of clearing and serial number. The hasError() function of the result object can be used to see if the
account information is valid. The result object will contain at most one error.
```php
$result = BankAccountValidator::withAccount('9661', '1231236');
if ($result->hasError()) {
    echo 'The account information is invalid' . PHP_EOL;
    if ($result->hasInvalidSerialNumberFormat()) {
        echo 'Invalid account format for "' . $result->getBankName() . '": ' . $result->getEnglishErrorMessage();
    } elseif ($result->hasInvalidChecksum()) {
        echo 'Invalid checksum for "' . $result->getBankName() . '": ' . $result->getEnglishErrorMessage();
    }
}
```

In some rare cases actual Swedbank accounts has a bad checksum. The result object distinguishes between
this and regular checksum errors.
```php
if ($result->hasInvalidSwedbankChecksum()) {
    echo 'Bad checksum but possibly correct for "' . $result->getBankName() . '": ' . $result->getEnglishErrorMessage();
} elseif ($result->hasInvalidChecksum()) {
    echo 'Bad checksum for "' . $result->getBankName() . '": ' . $result->getEnglishErrorMessage();
}
```

The result object contains helpful information about the validated account.
```php
$result = BankAccountValidator::withAccount('9661', '1231236');
echo 'Valid account' . PHP_EOL;
echo 'Bank: ' . $result->getBankName() . PHP_EOL;
echo 'Clearingnr: ' . $result->getClearingNumber() . PHP_EOL;
echo 'Serialnr: ' . $result->getSerialNumber() . PHP_EOL;
```

## System requirements
- **PHP v >= 5.5.0**

## License

[MIT license](http://opensource.org/licenses/MIT)