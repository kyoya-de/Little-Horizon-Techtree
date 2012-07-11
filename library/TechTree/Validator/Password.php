<?php
class TechTree_Validator_Password extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );

    /**
     * Validates the password and its confirmation.
     *
     * @param mixed $value   The given password.
     * @param array $context An array with information about the context.
     *
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $value = (string)$value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context['password_confirm']) &&
                ($value == $context['password_confirm'])
            ) {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}
