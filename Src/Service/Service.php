<?php

namespace Service;

class Service
{
    public static function slugIt($name)
    {
        $slug = strtolower(trim(
            preg_replace(
                '/[\s-]+/',
                '-',
                preg_replace(
                    '/[^A-Za-z0-9-]+/',
                    '-',
                    preg_replace(
                        '/[&]/',
                        'and',
                        preg_replace(
                            '/[\']/',
                            '',
                            iconv('UTF-8', 'ASCII//TRANSLIT', $name)
                        )
                    )
                )
            ),
            '-'
        ));

        return $slug;
    }
}
