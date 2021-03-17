<?php

namespace Portal\Validators;

class StringValidator
{
    public const MAXVARCHARLENGTH = 255;
    public const MAXTEXTLENGTH = 10000;

    /**
     * Validate that a string exists and is within length allowed, throws an error if not
     *
     * @param string $validateData
     * @param int $characterLength
     * @return string, which will return the validateData
     * @throws \Exception if the array is empty
     */
    public static function validateExistsAndLength(string $validateData, int $characterLength): string
    {
        if (empty($validateData) == false && strlen($validateData) <= $characterLength) {
            return $validateData;
        } else {
            throw new \Exception('An input string does not exist or is too long');
        }
    }

    /**
     * Validate that a string is not empty and is within length allowed, throws an error if not
     *
     * @param string $validateData
     * @param int $characterLength
     * @param string $fieldName
     * @return bool which will return the validateData or assigns to null
     * @throws \Exception if the data exceeds max length
     *
     */
    public static function validateLength(
        string $validateData,
        int $characterLength,
        string $fieldName = 'Unknown'
    ): bool {
        if (strlen($validateData) > $characterLength) {
            throw new \Exception(
                $fieldName . ' input string is too long, expected ' . $characterLength . ' characters or less'
            );
        }
        return true;
    }
}
