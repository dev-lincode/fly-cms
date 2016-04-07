<?php

namespace Lincode\Fly\Bundle\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Service {

	protected $container;

	public function __construct($container) {
		$this->container = $container;
	}

	/**
	 * Shortcut to return the Doctrine Registry service.
	 *
	 * @return Registry
	 *
	 * @throws \LogicException If DoctrineBundle is not available
	 */
	public function getDoctrine() {
		if (!$this->container->has('doctrine')) {
			throw new \LogicException('The DoctrineBundle is not registered in your application.');
		}

		return $this->container->get('doctrine');
	}

	/**
	 * Generates a URL from the given parameters.
	 *
	 * @param string      $route         The name of the route
	 * @param mixed       $parameters    An array of parameters
	 * @param bool|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
	 *
	 * @return string The generated URL
	 *
	 * @see UrlGeneratorInterface
	 */
	public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
		return $this->container->get('router')->generate($route, $parameters, $referenceType);
	}

	/**
	 * Creates and returns a Form instance from the type of the form.
	 *
	 * @param string|FormTypeInterface $type    The built type of the form
	 * @param mixed                    $data    The initial data for the form
	 * @param array                    $options Options for the form
	 *
	 * @return Form
	 */
	public function createForm($type, $data = null, array $options = array()) {
		return $this->container->get('form.factory')->create($type, $data, $options);
	}

	/**
	 * Creates and returns a form builder instance.
	 *
	 * @param mixed $data    The initial data for the form
	 * @param array $options Options for the form
	 *
	 * @return FormBuilder
	 */
	public function createFormBuilder($data = null, array $options = array()) {
		return $this->container->get('form.factory')->createBuilder('form', $data, $options);
	}

	/**
	 * Creates and returns a Form instance from the type of the form.
	 *
	 * @param string                   $name    The name of the form
	 * @param string|FormTypeInterface $type    The built type of the form
	 * @param mixed                    $data    The initial data for the form
	 * @param array                    $options Options for the form
	 *
	 * @return Form
	 */
	public function createNamedForm($name, $type, $data = null, array $options = array()) {
		return $this->container->get('form.factory')->createNamedBuilder($name, $type, $data, $options);
	}

	public function getRequest() {
		return $this->container->get('request');
	}

	public function getReferer() {
		$request = $this->container->get('request');
		$referer = $request->headers->get('referer');
		$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBaseUrl();
		return str_replace($baseurl, "", explode("?", $referer)[0]);
	}

	/**
	 * Returns a rendered view.
	 *
	 * @param string $view       The view name
	 * @param array  $parameters An array of parameters to pass to the view
	 *
	 * @return string The rendered view
	 */
	public function renderView($view, array $parameters = array()) {
		return $this->container->get('templating')->render($view, $parameters);
	}

	/**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     *
     * @param string          $message  A message
     * @param \Exception|null $previous The previous exception
     *
     * @return NotFoundHttpException
     */
    public function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }

    /**
     * Renders a view.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if ($this->container->has('templating')) {
            return $this->container->get('templating')->renderResponse($view, $parameters, $response);
        }

        if (!$this->container->has('twig')) {
            throw new \LogicException('You can not use the "render" method if the Templating Component or the Twig Bundle are not available.');
        }

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->container->get('twig')->render($view, $parameters));

        return $response;
    }
}
