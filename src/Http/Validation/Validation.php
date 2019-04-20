<?php

namespace Snap\Http\Validation;

use Snap\Services\Container;

/**
 * A handy wrapper around Rakit\Validation.
 */
class Validation
{
    /**
     * @var \Rakit\Validation\ErrorBag
     */
    public static $errors;

    /**
     * Holds the Validator singleton.
     *
     * @var \Rakit\Validation\Validator
     */
    private $validator;

    /**
     * Holds the Validation instance.
     *
     * @var \Rakit\Validation\Validation
     */
    private $validation;

    /**
     * Validation constructor.
     *
     * @param null|array $data     Optional. Input data to validate.
     * @param array      $rules    Optional. Validation rules array.
     * @param array      $messages Optional. Validation messages.
     */
    public function __construct($data = null, array $rules = [], array $messages = [])
    {
        $this->validator = Container::get('Rakit\Validation\Validator');

        if ($data !== null) {
            $this->validation = $this->validator->make(
                $data,
                []
            );
        }

        if (!empty($rules)) {
            $this->set_rules($rules);
        }

        if (!empty($messages)) {
            $this->set_error_messages($messages);
        }

        static::$errors = $this->validation->errors();
    }

    /**
     * Manually validate any given array.
     *
     * @param  array $inputs   The array of data to validate as key value pairs.
     * @param  array $rules    The rules to run against the data.
     * @param  array $messages Messages to display when a value fails.
     * @return bool|array Returns true if data validates, or an array of error messages.
     */
    public static function validate(array $inputs, array $rules = [], array $messages = [])
    {
        /** @var \Rakit\Validation\Validation $validation */
        $validation = Container::get('Rakit\Validation\Validator')->validate($inputs, $rules, $messages);

        if ($validation->fails()) {
            return $validation->errors()->toArray();
        }

        return true;
    }

    /**
     * Set the validation error messages.
     *
     * @see    https://github.com/rakit/validation#custom-validation-message for format.
     *
     * @param array $messages Error messages as key value pairs.
     * @return Validation
     */
    public function set_error_messages(array $messages = [])
    {
        $this->validation->setMessages($messages);

        return $this;
    }

    /**
     * Set the validation rules.
     *
     * @see    https://github.com/rakit/validation#available-rules for format.
     *
     * @param array $rule_set Rules as key value pairs.
     * @return Validation
     */
    public function set_rules(array $rule_set = [])
    {
        foreach ($rule_set as $attribute_key => $rules) {
            $this->validation->addAttribute($attribute_key, $rules);
        }

        return $this;
    }

    /**
     * Set aliases for use in your error messages.
     *
     * In error messages :attribute can be used to substitute with the input array key into the message.
     * The key might not be ideal, so you can provide a better substitute as an alias.
     *
     * @param array $aliases Key value pairs as original => alias.
     */
    public function set_aliases(array $aliases = [])
    {
        $this->validation->setAliases($aliases);
    }

    /**
     * Validates the request using the rules and messages set on the internal validation instance.
     *
     * @return boolean If the validation passed or not.
     */
    public function is_valid(): bool
    {
        $this->validation->validate();
        static::$errors = $this->validation->errors();

        return !$this->validation->fails();
    }

    /**
     * Get errors from the internal validation instance as a multi-dimensional array.
     *
     * @param null   $key    Optional. Specific key to fetch errors for.
     * @param string $format Optional. Format string for the fetched error when using $key.
     * @return array
     */
    public function get_errors($key = null, string $format = ':message'): array
    {
        if (null === $key) {
            return \array_map(
                function ($vals) {
                    return \array_values($vals);
                },
                $this->validation->errors()->toArray()
            );
        }

        return $this->validation->errors()->get($key, $format);
    }

    /**
     * Get count of all errors.
     *
     * @return int
     */
    public function get_error_count(): int
    {
        return $this->validation->errors()->count();
    }

    /**
     * Returns a flat array of all errors.
     *
     * @param string $format Optional. Format to wrap errors in such as '<li>:message</li>'.
     *                       Defaults to ':message'.
     * @return array
     */
    public function get_all_errors(string $format = ':message'): array
    {
        return $this->validation->errors()->all($format);
    }

    /**
     * Checks if an error exists.
     *
     * @param string $key The key to search for. EG. 'name' or 'uploads.*'.
     * @return bool
     */
    public function has_error(string $key): bool
    {
        return $this->validation->errors()->has($key);
    }
}
