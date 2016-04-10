<?php

namespace Lincode\Fly\Bundle\Service;

class PermissionService {

	protected $container;

	public function __construct($container) {
		$this->container = $container;
	}

	public function hasParams($entityParams, $params) {
		if(!is_array($entityParams)) {
			$entityParams = json_decode($entityParams);
		}
		if(count($entityParams) > 0) {
			foreach($entityParams as $key => $value) {
				if(!array_key_exists($key, $params) || $params[$key] != $value) {
					return false;
				}
			}
		}
		return true;
	}

	public function getJson() {

		$json = json_decode(file_get_contents( $this->container->get('kernel')->getRootDir() . "/../src/CMS/config.json"), true);
		if($json === NULL) {
			throw new \Exception('CONFIG JSON - ' . $this->getJsonError());
		}

		return $json;
	}

	private function getJsonError() {
		static $errors = array(
			JSON_ERROR_NONE             => 'Não possui erros.',
			JSON_ERROR_DEPTH            => 'A profundidade máxima da pilha foi excedida.',
			JSON_ERROR_STATE_MISMATCH   => 'JSON inválido ou incorreto.',
			JSON_ERROR_CTRL_CHAR        => 'Erro de caractere de controle. Possivelmente escrito incorretamente.',
			JSON_ERROR_SYNTAX           => 'Erro de sintaxe.',
			JSON_ERROR_UTF8             => 'Erro de formatação UTF-8. Possivelmente codificado incorretamente.'
		);

		$error = json_last_error();
		return array_key_exists($error, $errors) ? $errors[$error] : "Erro desconhecido ({$error})";
	}
}
