=== Plugin Name ===
Contributors: husanisantos
Tags: woocommerce, order, whatsapp, button, buy
Requires at least: 3.6
Tested up to: 5.8.9
Requires PHP: 5.6


Apenas um plugin que altera o botão de adicionar ao carrinho do Woocommerce para um chat com vendedores no whatsapp.

== Description ==

Com o Whats Order você cadastra vendedores em seu Wordpress para que a venda seja direcionada para o whatsapp do vendedor cadastrado.

Funciona de maneira muito simples, o administrador cadastra os vendedores, e cada um recebe um identificador e uma url e quando
um cliente acessar através desse link, o sistema armazena a informação e direciona todos os contatos que esse cliente fizer ao
vendedor em questão.


== Installation ==

1. Suba o arquivo `whats-order.zip` para o diretório `/wp-content/plugins/`
1. Ative o plugin no menu 'Plugins' do Wordpress

== FAQ ==

= O que é necessário para o plugin funcionar bem? =

Recomendável que a versão do PHP seja 7.2 ou superior.
É necessário que tenha o Woocommerce instalado.

= O que o plugin faz? =

Basicamente remove o botão de compra e adicionar ao carrinho do woocommerce e no lugar adiciona
um botão para conversar sobre o produto em questão no whatsapp.

== Changelog ==

=1.0.5=
Correção da função OpenWhatsapp().

=1.0.4=
Correção de BUG ao enviar dados ao WhatsApp.

=1.0.3=
Adição de estoque disponível na página do produto.

=1.0.2=
Correção de UI e Botões.

=1.0.1=
Correção de BUG.

== Notas ==

=1.0.2=
Correção do botão "Configurações" ao acessar página de configurações, UI e remoção de classes desnecessárias.

=1.0.1= 
Correção de bug ao abrir o link do whatsapp em dispositivos mobile.
