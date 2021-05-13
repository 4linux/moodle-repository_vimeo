<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language file
 *
 * @package    repository_vimeo
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Videos Vimeo';
$string['access_token'] = 'Token de acesso Vimeo';
$string['clientid'] = 'Chave client identifier';
$string['clientsecret'] = 'Chave client secret';
$string['configplugin'] = 'Configuração do repositório Vimeo';
$string['vimeo:view'] = 'Visualizar o repositório Vimeo';
$string['authenticatebutton'] = 'Autenticar';
$string['authenticatebuttonhelper'] = "Clique no botão Autenticar para gerar o token de autenticação. Você será direcionado para a plataforma Vimeo para permitir o acesso aos vídeos";
$string['howauthenticate'] = "Para realizar a autenticação, siga os seguintes passos:
<ol>
    <li>Preencha o campo ID do cliente (Pode ser obtido no seu <a href='https://developer.vimeo.com/apps' target='_blank'>aplicativo do Vimeo</a>);</li>
    <li>Preencha o campo segredo do cliente (Pode ser obtido no seu <a href='https://developer.vimeo.com/apps' target='_blank'>aplicativo do Vimeo</a>);</li>
    <li>Clique em salvar;</li>
    <li>Retorne para essa configuração; e</li>
    <li>Pressione o botão autenticar.</li>
</ol> 
";
$string['cannotgeneratetoken'] ="Não foi possível gerar o Token de autenticação";
$string['cannotgeneratecode'] ="Não foi possível obter código de autorização";
$string['successfullyauthenticated'] = "Autenticação realizada com sucesso";
$string['vimeoauthentication'] = "Autenticação Vimeo";
$string['sort'] = 'Ordenar';
$string['search'] = 'Buscar';
$string['searchby'] = 'Buscar por';
$string['direction'] = 'Direção';
$string['date'] = 'Data';
$string['duration'] = 'Duração';
$string['alphabetical'] = 'Alfabética';
$string['default'] = 'Padrão';
$string['likes'] = 'Curtidas';
$string['modifiedtime'] = 'Horário de modificação';
$string['plays'] = 'Reproduzidas';
$string['asc'] = 'Ascendente';
$string['desc'] = 'Descendente';
$string['privacy:metadata'] = 'O plugin repositório Vimeo não armazena nenhum dado pessoal.';