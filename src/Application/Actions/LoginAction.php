<?php

namespace Application\Actions;

use Domain\Interfaces\RootServiceInterface;
use Infrastructure\Abstractions\AbstractAction;
use Infrastructure\Facades\Auth;
use Presentation\Interfaces\LoginResponderInterface;
use Psr\Http\Message\ResponseInterface;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

final class LoginAction extends AbstractAction
{
    public function __construct(
        private RootServiceInterface $rootService,
        private LoginResponderInterface $responder
    ) {
    }

    public function get(): ResponseInterface
    {
        return $this->respond($this->responder);
    }

    public function post(): ResponseInterface
    {
        $body = $this->request->getParsedBody();

        $email = null;
        $password = null;
        $isLoggedIn = false;

        if (empty($body['SAMLResponse']) === false) {
            // Its a saml response

            $decodedBody = base64_decode($body['SAMLResponse']);

            $myKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'public']);
            $myKey->loadKey(__DIR__ . '/../../okta.cert', true);

            // Get the signature node

            $doc = new \DOMDocument();
            $doc->loadXML($decodedBody);
            $elements = $doc->getElementsByTagName('Signature');
            $node = null;
            foreach ($elements as $element) {
                $node = $element;
                break;
            }

            // Create a signature from the node

            $signature = new XMLSecurityDSig();
            $signature->idKeys[] = 'ID';
            $signature->sigNode = $node;
            $signature->canonicalizeSignedInfo();

            // Validate

            $signature->validateReference();
            $isSuccess = $signature->verify($myKey) === 1;

            // Get claims

            $claims = [];
            $tags = $doc->getElementsByTagName('Attribute');
            foreach($tags as $tag) {
                $attrKey = null;
                foreach($tag->attributes as $attribute) {
                    if ($attribute->name === 'Name') {
                        $attrKey = $attribute->value;
                    }
                }

                if ($attrKey !== null) {
                    $claims[$attrKey] = $tag->nodeValue;
                }
            }

            var_dump($claims);

            echo '<textarea>';
            print_r($decodedBody);
            echo '</textarea>';

            dd($isSuccess);

            // find existing user by email or sso_id
            // if they exist log them in
            // if they don't exist create a user with the claims and log them in
        } else {
            $email = $body['email'];
            $password = $body['password'];
        }

        if (
            empty($email) === false
            && empty($password) === false
        ) {
            $isLoggedIn = Auth::login(
                $email,
                $password
            );
        }

        return $this->respond($this->responder, [
            'isLoggedIn' => $isLoggedIn
        ]);
    }
}