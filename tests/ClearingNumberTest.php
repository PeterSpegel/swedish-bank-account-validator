<?php
namespace SwedishBankAccountValidator;

use PHPUnit_Framework_TestCase;

class ClearingNumberTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $clearingNumber
     * @dataProvider validClearingNumberDataProvider
     */
    public function test_construction_of_instance_with_valid_clearing_number($clearingNumber)
    {
        $this->assertInstanceOf(ClearingNumber::class, new ClearingNumber($clearingNumber));
    }

    public function validClearingNumberDataProvider()
    {
        return [
            ['9449'],
            ['9449'],
            ['9449'],
            ['9180'],
            ['6875'],
            ['9960'],
        ];
    }

    /**
     * @param int $invalidClearing
     * @dataProvider invalidClearingNumberDataProvider
     */
    public function test_that_invalid_clearing_number_length_throws_exception($invalidClearing)
    {
        $result = ClearingNumber::validate($invalidClearing);
        $this->assertTrue($result->hasError());
        $this->assertTrue($result->hasInvalidClearingNumber());
    }

    public function invalidClearingNumberDataProvider()
    {
        return [
            ['ABCD'],
            ['12'],
            ['123'],
            ['0122'],
            ['999'],
        ];
    }

    public function test_that_only_the_first_4_digits_is_used()
    {
        $clearingNumber = new ClearingNumber('84202');
        $this->assertEquals('8420', $clearingNumber->__toString());
    }
}
