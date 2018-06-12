<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-27 10:49
 */
use FastD\Http\ServerRequest;
use Moment\Validator\Exceptions\ValidationException;
use Moment\Validator\Validator;

if (!function_exists('validator_moment')) {
    function validator_moment(ServerRequest $request, array $rules)
    {
        $data = [];
        foreach ($rules as $field => $rule) {
            $field = explode('.', $field);
            $field = array_shift($field);
            if (array_key_exists($field, $data)) {
                continue;
            }
            if (isset($request->queryParams[$field])) {
                $data[$field] = $request->queryParams[$field];
            } elseif (isset($request->bodyParams[$field])) {
                $data[$field] = $request->bodyParams[$field];
            } elseif(isset($request->attributes[$field])) {
                $data[$field] = $request->attributes[$field];
            }
        }

        $validator = new Validator($data, $rules);
        if (!$validator->validate()) {
            $messages = '';
            foreach ($validator->messages() as $fieldMessages) {
                $messages .= implode(';', $fieldMessages).';';
            }

            throw new ValidationException($messages, 422);
        }

        return $validator;
    }
}
