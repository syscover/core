<?php namespace Syscover\Core\Middleware;

use Closure;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class GraphQL extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->input('operationName') !== 'CoreGetBootstrapConfig')
        {
            //********************************
            //* Check middleware jwt.auth
            //********************************
            if (! $token = $this->auth->setRequest($request)->getToken()) {
                return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
            }

            try {
                $user = $this->auth->authenticate($token);
            } catch (TokenExpiredException $e) {
                return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
            } catch (JWTException $e) {
                return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
            }

            if (! $user) {
                return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
            }

            $this->events->fire('tymon.jwt.valid', $user);

            //********************************
            //* Check middleware jwt.refresh
            //********************************
            $response = $next($request);

            try {
                $newToken = $this->auth->setRequest($request)->parseToken()->refresh();
            } catch (TokenExpiredException $e) {
                return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
            } catch (JWTException $e) {
                return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
            }

            // send the refreshed token back to the client
            $response->headers->set('Authorization', 'Bearer ' . $newToken);

            return $response;
        }

        return $next($request);
    }
}
