<?php

namespace Domain\Services;

use Application\Exceptions\SamlException;
use Domain\Interfaces\SamlServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

final class SamlService implements SamlServiceInterface
{
    public function isRequestSAML(ServerRequestInterface $request): bool
    {
        $body = $request->getParsedBody();

        if (empty($body['SAMLResponse']) === false) {
            $xml = $this->decodeSAMLFromResponse($body['SAMLResponse']);
            if (str_starts_with($xml, '<?xml version="1.0" encoding="UTF-8"?>') === true) {
                return true;
            }
        }

        return false;
    }

    public function getSAMLFromRequest(ServerRequestInterface $request): string
    {
        if ($this->isRequestSAML($request) === false) {
            throw new SamlException('Request Not Saml');
        }

        return $this->decodeSAMLFromResponse($request->getParsedBody()['SAMLResponse']);
    }

    public function verifySignedSAML(string $saml): bool
    {
        // TODO: Key path need to be env var
        $key = $this->loadKey(__DIR__ . '/../../okta.cert');
        $dom = $this->loadDOMFromSAML($saml);

        $signature = $this->loadSignature($dom);
        $signature->validateReference();

        return $signature->verify($key) === 1;
    }

    public function getAttributeStatementsFromSAML(string $saml): array
    {
        $dom = $this->loadDOMFromSAML($saml);

        $attributeStatements = [];
        $tags = $dom->getElementsByTagName('Attribute');
        foreach($tags as $tag) {
            $attributeKey = null;
            foreach($tag->attributes as $attribute) {
                if ($attribute->name === 'Name') {
                    $attributeKey = $attribute->value;
                }
            }

            if ($attributeKey !== null) {
                $attributeStatements[$attributeKey] = $tag->nodeValue;
            }
        }

        return $attributeStatements;
    }

    public function getClaims(string $saml): array
    {
        return $this->getAttributeStatementsFromSAML($saml);
    }

    private function loadDOMFromSAML(string $saml): \DOMDocument
    {
        $dom = new \DOMDocument();
        $dom->loadXML($saml);

        return $dom;
    }

    private function loadSignature(\DOMDocument $dom): XMLSecurityDSig
    {
        $tags = $dom->getElementsByTagName('Signature');
        $signatureNode = null;
        foreach ($tags as $tag) {
            $signatureNode = $tag;
            break;
        }

        $signature = new XMLSecurityDSig();
        $signature->idKeys[] = 'ID';
        $signature->sigNode = $signatureNode;
        $signature->canonicalizeSignedInfo();

        return $signature;
    }

    private function loadKey(string $path): XMLSecurityKey
    {
        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'public']);
        $key->loadKey($path, true);

        return $key;
    }

    private function decodeSAMLFromResponse(string $samlResponse): string
    {
        return base64_decode($samlResponse);
    }
}