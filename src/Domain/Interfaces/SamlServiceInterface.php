<?php

namespace Domain\Interfaces;

use Infrastructure\Interfaces\ServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

interface SamlServiceInterface extends ServiceInterface
{
    public function isRequestSAML(ServerRequestInterface $request): bool;
    public function getSAMLFromRequest(ServerRequestInterface $request): string;
    public function verifySignedSAML(string $saml): bool;
    public function getAttributeStatementsFromSAML(string $saml): array;
    public function getClaims(string $saml): array;
}