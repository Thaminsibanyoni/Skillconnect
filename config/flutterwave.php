<?php

return [
    /**
     * Public Key: Your Flutterwave public key.
     *
     */
    'publicKey' => env('FLUTTERWAVE_PUBLIC_KEY'),

    /**
     * Secret Key: Your Flutterwave secret key.
     *
     */
    'secretKey' => env('FLUTTERWAVE_SECRET_KEY'),

    /**
     * Secret Hash: Used to validate webhook signatures.
     *
     */
    'secretHash' => env('FLUTTERWAVE_SECRET_HASH', ''),

    /**
     * Encryption Key: Used for encrypting payload upon request.
     * It is generated automatically using the command: php artisan flutterwave:encrypt.
     * You should leave this as is.
     */
    // 'encryptionKey' => env('FLUTTERWAVE_ENCRYPTION_KEY'), // Encryption seems optional/separate
];
