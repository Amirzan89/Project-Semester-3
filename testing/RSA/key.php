<?php

// Generate a new private key
$privateKey = openssl_pkey_new(array(
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
));

// Extract the private key from $privateKey to $privateKeyStr
openssl_pkey_export($privateKey, $privateKeyStr);

// Generate a new public key
$publicKey = openssl_pkey_get_details($privateKey)["key"];

// Data to encrypt
$data = "secret message";

// Encrypt the data using the public key
openssl_public_encrypt($data, $encryptedData, $publicKey);

// Decrypt the data using the private key
openssl_private_decrypt($encryptedData, $decryptedData, $privateKeyStr);

// Output the original data and the decrypted data
echo "Original Data: " . $data . "\n";
echo "Decrypted Data: " . $decryptedData . "\n";

// // Generate key pair
// $privateKeyResource = openssl_pkey_new(array(
//     'private_key_bits' => 2048,  // Adjust as needed
//     'private_key_type' => OPENSSL_KEYTYPE_RSA,
// ));

// // Check if key generation was successful
// if (!$privateKeyResource) {
//     die("Key generation failed.");
// }

// // Extract public key
// $keyDetails = openssl_pkey_get_details($privateKeyResource);

// // Check if key details extraction was successful
// if (!$keyDetails) {
//     die("Failed to get key details.");
// }

// $publicKey = $keyDetails['key'];
// $privateKey = $keyDetails['rsa'];

// echo "Public Key:\n" . $publicKey . PHP_EOL;
// echo "\nPrivate Key:\n" . $privateKey . PHP_EOL;

// // Data to be encrypted
// $data = "Hello, World!";

// // Encrypt using public key
// openssl_public_encrypt($data, $encrypted, $publicKey);

// echo "\nEncrypted: " . base64_encode($encrypted) . PHP_EOL;

// // Decrypt using private key
// openssl_private_decrypt($encrypted, $decrypted, $privateKeyResource);

// echo "\nDecrypted: " . $decrypted . PHP_EOL;

// // Free the key from memory
// openssl_free_key($privateKeyResource);
?>
