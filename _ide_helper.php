<?php
/* @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile

/**
 * A helper file for Laravel, to provide autocomplete information to your IDE
 * Generated for Laravel 12.8.1.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */
namespace Barryvdh\DomPDF\Facade {
    /**
     * 
     *
     * @method static BasePDF setBaseHost(string $baseHost)
     * @method static BasePDF setBasePath(string $basePath)
     * @method static BasePDF setCanvas(\Dompdf\Canvas $canvas)
     * @method static BasePDF setCallbacks(array<string, mixed> $callbacks)
     * @method static BasePDF setCss(\Dompdf\Css\Stylesheet $css)
     * @method static BasePDF setDefaultView(string $defaultView, array<string, mixed> $options)
     * @method static BasePDF setDom(\DOMDocument $dom)
     * @method static BasePDF setFontMetrics(\Dompdf\FontMetrics $fontMetrics)
     * @method static BasePDF setHttpContext(resource|array<string, mixed> $httpContext)
     * @method static BasePDF setPaper(string|float[] $paper, string $orientation = 'portrait')
     * @method static BasePDF setProtocol(string $protocol)
     * @method static BasePDF setTree(\Dompdf\Frame\FrameTree $tree)
     */

    class Pdf {
        /**
         * Get the DomPDF instance
         *
         * @static 
         */
        public static function getDomPDF()
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->getDomPDF();
        }

        /**
         * Show or hide warnings
         *
         * @static 
         */
        public static function setWarnings($warnings)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->setWarnings($warnings);
        }

        /**
         * Load a HTML string
         *
         * @param string|null $encoding Not used yet
         * @static 
         */
        public static function loadHTML($string, $encoding = null)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->loadHTML($string, $encoding);
        }

        /**
         * Load a HTML file
         *
         * @static 
         */
        public static function loadFile($file)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->loadFile($file);
        }

        /**
         * Add metadata info
         *
         * @param array<string, string> $info
         * @static 
         */
        public static function addInfo($info)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->addInfo($info);
        }

        /**
         * Load a View and convert to HTML
         *
         * @param array<string, mixed> $data
         * @param array<string, mixed> $mergeData
         * @param string|null $encoding Not used yet
         * @static 
         */
        public static function loadView($view, $data = [], $mergeData = [], $encoding = null)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->loadView($view, $data, $mergeData, $encoding);
        }

        /**
         * Set/Change an option (or array of options) in Dompdf
         *
         * @param array<string, mixed>|string $attribute
         * @param null|mixed $value
         * @static 
         */
        public static function setOption($attribute, $value = null)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->setOption($attribute, $value);
        }

        /**
         * Replace all the Options from DomPDF
         *
         * @param array<string, mixed> $options
         * @static 
         */
        public static function setOptions($options, $mergeWithDefaults = false)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->setOptions($options, $mergeWithDefaults);
        }

        /**
         * Output the PDF as a string.
         * 
         * The options parameter controls the output. Accepted options are:
         * 
         * 'compress' = > 1 or 0 - apply content stream compression, this is
         *    on (1) by default
         *
         * @param array<string, int> $options
         * @return string The rendered PDF as string
         * @static 
         */
        public static function output($options = [])
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->output($options);
        }

        /**
         * Save the PDF to a file
         *
         * @static 
         */
        public static function save($filename, $disk = null)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->save($filename, $disk);
        }

        /**
         * Make the PDF downloadable by the user
         *
         * @static 
         */
        public static function download($filename = 'document.pdf')
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->download($filename);
        }

        /**
         * Return a response with the PDF to show in the browser
         *
         * @static 
         */
        public static function stream($filename = 'document.pdf')
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->stream($filename);
        }

        /**
         * Render the PDF
         *
         * @static 
         */
        public static function render()
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->render();
        }

        /**
         * 
         *
         * @param array<string> $pc
         * @static 
         */
        public static function setEncryption($password, $ownerpassword = '', $pc = [])
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->setEncryption($password, $ownerpassword, $pc);
        }

            }
    }

namespace Tymon\JWTAuth\Facades {
    /**
     * 
     *
     */
    class JWTAuth {
        /**
         * Attempt to authenticate the user and return the token.
         *
         * @param array $credentials
         * @return false|string 
         * @static 
         */
        public static function attempt($credentials)
        {
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->attempt($credentials);
        }

        /**
         * Authenticate a user via a token.
         *
         * @return \Tymon\JWTAuth\Contracts\JWTSubject|false 
         * @static 
         */
        public static function authenticate()
        {
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->authenticate();
        }

        /**
         * Alias for authenticate().
         *
         * @return \Tymon\JWTAuth\Contracts\JWTSubject|false 
         * @static 
         */
        public static function toUser()
        {
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->toUser();
        }

        /**
         * Get the authenticated user.
         *
         * @return \Tymon\JWTAuth\Contracts\JWTSubject 
         * @static 
         */
        public static function user()
        {
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->user();
        }

        /**
         * Generate a token for a given subject.
         *
         * @param \Tymon\JWTAuth\Contracts\JWTSubject $subject
         * @return string 
         * @static 
         */
        public static function fromSubject($subject)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->fromSubject($subject);
        }

        /**
         * Alias to generate a token for a given user.
         *
         * @param \Tymon\JWTAuth\Contracts\JWTSubject $user
         * @return string 
         * @static 
         */
        public static function fromUser($user)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->fromUser($user);
        }

        /**
         * Refresh an expired token.
         *
         * @param bool $forceForever
         * @param bool $resetClaims
         * @return string 
         * @static 
         */
        public static function refresh($forceForever = false, $resetClaims = false)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->refresh($forceForever, $resetClaims);
        }

        /**
         * Invalidate a token (add it to the blacklist).
         *
         * @param bool $forceForever
         * @return \Tymon\JWTAuth\JWTAuth 
         * @static 
         */
        public static function invalidate($forceForever = false)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->invalidate($forceForever);
        }

        /**
         * Alias to get the payload, and as a result checks that
         * the token is valid i.e. not expired or blacklisted.
         *
         * @return \Tymon\JWTAuth\Payload 
         * @throws \Tymon\JWTAuth\Exceptions\JWTException
         * @static 
         */
        public static function checkOrFail()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->checkOrFail();
        }

        /**
         * Check that the token is valid.
         *
         * @param bool $getPayload
         * @return \Tymon\JWTAuth\Payload|bool 
         * @static 
         */
        public static function check($getPayload = false)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->check($getPayload);
        }

        /**
         * Get the token.
         *
         * @return \Tymon\JWTAuth\Token|null 
         * @static 
         */
        public static function getToken()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->getToken();
        }

        /**
         * Parse the token from the request.
         *
         * @return \Tymon\JWTAuth\JWTAuth 
         * @throws \Tymon\JWTAuth\Exceptions\JWTException
         * @static 
         */
        public static function parseToken()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->parseToken();
        }

        /**
         * Get the raw Payload instance.
         *
         * @return \Tymon\JWTAuth\Payload 
         * @static 
         */
        public static function getPayload()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->getPayload();
        }

        /**
         * Alias for getPayload().
         *
         * @return \Tymon\JWTAuth\Payload 
         * @static 
         */
        public static function payload()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->payload();
        }

        /**
         * Convenience method to get a claim value.
         *
         * @param string $claim
         * @return mixed 
         * @static 
         */
        public static function getClaim($claim)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->getClaim($claim);
        }

        /**
         * Create a Payload instance.
         *
         * @param \Tymon\JWTAuth\Contracts\JWTSubject $subject
         * @return \Tymon\JWTAuth\Payload 
         * @static 
         */
        public static function makePayload($subject)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->makePayload($subject);
        }

        /**
         * Check if the subject model matches the one saved in the token.
         *
         * @param string|object $model
         * @return bool 
         * @static 
         */
        public static function checkSubjectModel($model)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->checkSubjectModel($model);
        }

        /**
         * Set the token.
         *
         * @param \Tymon\JWTAuth\Token|string $token
         * @return \Tymon\JWTAuth\JWTAuth 
         * @static 
         */
        public static function setToken($token)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->setToken($token);
        }

        /**
         * Unset the current token.
         *
         * @return \Tymon\JWTAuth\JWTAuth 
         * @static 
         */
        public static function unsetToken()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->unsetToken();
        }

        /**
         * Set the request instance.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Tymon\JWTAuth\JWTAuth 
         * @static 
         */
        public static function setRequest($request)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->setRequest($request);
        }

        /**
         * Set whether the subject should be "locked".
         *
         * @param bool $lock
         * @return \Tymon\JWTAuth\JWTAuth 
         * @static 
         */
        public static function lockSubject($lock)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->lockSubject($lock);
        }

        /**
         * Get the Manager instance.
         *
         * @return \Tymon\JWTAuth\Manager 
         * @static 
         */
        public static function manager()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->manager();
        }

        /**
         * Get the Parser instance.
         *
         * @return \Tymon\JWTAuth\Http\Parser\Parser 
         * @static 
         */
        public static function parser()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->parser();
        }

        /**
         * Get the Payload Factory.
         *
         * @return \Tymon\JWTAuth\Factory 
         * @static 
         */
        public static function factory()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->factory();
        }

        /**
         * Get the Blacklist.
         *
         * @return \Tymon\JWTAuth\Blacklist 
         * @static 
         */
        public static function blacklist()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->blacklist();
        }

        /**
         * Set the custom claims.
         *
         * @param array $customClaims
         * @return \Tymon\JWTAuth\JWTAuth 
         * @static 
         */
        public static function customClaims($customClaims)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->customClaims($customClaims);
        }

        /**
         * Alias to set the custom claims.
         *
         * @param array $customClaims
         * @return \Tymon\JWTAuth\JWTAuth 
         * @static 
         */
        public static function claims($customClaims)
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->claims($customClaims);
        }

        /**
         * Get the custom claims.
         *
         * @return array 
         * @static 
         */
        public static function getCustomClaims()
        {
            //Method inherited from \Tymon\JWTAuth\JWT 
            /** @var \Tymon\JWTAuth\JWTAuth $instance */
            return $instance->getCustomClaims();
        }

            }
    /**
     * 
     *
     */
    class JWTFactory {
        /**
         * Create the Payload instance.
         *
         * @param bool $resetClaims
         * @return \Tymon\JWTAuth\Payload 
         * @static 
         */
        public static function make($resetClaims = false)
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->make($resetClaims);
        }

        /**
         * Empty the claims collection.
         *
         * @return \Tymon\JWTAuth\Factory 
         * @static 
         */
        public static function emptyClaims()
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->emptyClaims();
        }

        /**
         * Build and get the Claims Collection.
         *
         * @return \Tymon\JWTAuth\Claims\Collection 
         * @static 
         */
        public static function buildClaimsCollection()
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->buildClaimsCollection();
        }

        /**
         * Get a Payload instance with a claims collection.
         *
         * @param \Tymon\JWTAuth\Claims\Collection $claims
         * @return \Tymon\JWTAuth\Payload 
         * @static 
         */
        public static function withClaims($claims)
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->withClaims($claims);
        }

        /**
         * Set the default claims to be added to the Payload.
         *
         * @param array $claims
         * @return \Tymon\JWTAuth\Factory 
         * @static 
         */
        public static function setDefaultClaims($claims)
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->setDefaultClaims($claims);
        }

        /**
         * Helper to set the ttl.
         *
         * @param int $ttl
         * @return \Tymon\JWTAuth\Factory 
         * @static 
         */
        public static function setTTL($ttl)
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->setTTL($ttl);
        }

        /**
         * Helper to get the ttl.
         *
         * @return int 
         * @static 
         */
        public static function getTTL()
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->getTTL();
        }

        /**
         * Get the default claims.
         *
         * @return array 
         * @static 
         */
        public static function getDefaultClaims()
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->getDefaultClaims();
        }

        /**
         * Get the PayloadValidator instance.
         *
         * @return \Tymon\JWTAuth\Validators\PayloadValidator 
         * @static 
         */
        public static function validator()
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->validator();
        }

        /**
         * Set the custom claims.
         *
         * @param array $customClaims
         * @return \Tymon\JWTAuth\Factory 
         * @static 
         */
        public static function customClaims($customClaims)
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->customClaims($customClaims);
        }

        /**
         * Alias to set the custom claims.
         *
         * @param array $customClaims
         * @return \Tymon\JWTAuth\Factory 
         * @static 
         */
        public static function claims($customClaims)
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->claims($customClaims);
        }

        /**
         * Get the custom claims.
         *
         * @return array 
         * @static 
         */
        public static function getCustomClaims()
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->getCustomClaims();
        }

        /**
         * Set the refresh flow flag.
         *
         * @param bool $refreshFlow
         * @return \Tymon\JWTAuth\Factory 
         * @static 
         */
        public static function setRefreshFlow($refreshFlow = true)
        {
            /** @var \Tymon\JWTAuth\Factory $instance */
            return $instance->setRefreshFlow($refreshFlow);
        }

            }
    }

namespace Illuminate\Http {
    /**
     * 
     *
     */
    class Request {
        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param array $rules
         * @param mixed $params
         * @static 
         */
        public static function validate($rules, ...$params)
        {
            return \Illuminate\Http\Request::validate($rules, ...$params);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param string $errorBag
         * @param array $rules
         * @param mixed $params
         * @static 
         */
        public static function validateWithBag($errorBag, $rules, ...$params)
        {
            return \Illuminate\Http\Request::validateWithBag($errorBag, $rules, ...$params);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $absolute
         * @static 
         */
        public static function hasValidSignature($absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignature($absolute);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @static 
         */
        public static function hasValidRelativeSignature()
        {
            return \Illuminate\Http\Request::hasValidRelativeSignature();
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @param mixed $absolute
         * @static 
         */
        public static function hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignatureWhileIgnoring($ignoreQuery, $absolute);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @static 
         */
        public static function hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
        {
            return \Illuminate\Http\Request::hasValidRelativeSignatureWhileIgnoring($ignoreQuery);
        }

            }
    }


namespace  {
    class PDF extends \Barryvdh\DomPDF\Facade\Pdf {}
    class JWTAuth extends \Tymon\JWTAuth\Facades\JWTAuth {}
    class JWTFactory extends \Tymon\JWTAuth\Facades\JWTFactory {}
}





