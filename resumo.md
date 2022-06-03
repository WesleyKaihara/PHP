# Web Services 

- Utiliza protocolo HTPP
- Mensagem baseado em XML

## Protocolo SOAP

- Simple Objetct Acess Protocol
- Protocolo Simples de Acesso a Objetos

- Formada por 3 Elementos:
  - Envelope 
  - SOAP Header 
  - SOAP Body

### Envelope 
- Elemento raiz da mensagem 
- Define um Documento XML como mensagem SOAP

### Header 
- Elemento opcional no XML
- Quando usado é o primerio elemento filho
- Podem ser colocadas informações extras para passar ao servidor.(Como informações de segurança)
- Header SOAP != Header HTTP

### Body
- Elemento obrigatório 
- Contém os dados de negocio que o servidor esta esperando

#

### Fault 
- Elemento é utilizado quando ocorrem falhas 
- Mensagem de falha
- faultstring -> descrição da falha 

``` xml
<?xml version="1.0"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
    <soap:Fault>
     <faultcode>soap:Client</faultcode>
     <faultstring>Falha ao consultar clientes</faultstring>
      </soap:Fault>
</soap:Body>
</soap:Envelope>
```

#

## Exemplo de Comunicação utilizando WebService SOAP:

```xml
REQUEST - REQUISIÇÃO:
POST /consultaCliente HTTP/1.1
Host: www.minhaempresa.com
Content-Type: application/soap+xml; charset=utf-8
Content-Length: nnn
 
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body xmlns:m="http://www.minhaempresa.com/consultaCliente">
  <m:consultaClienteRequest>
    <m:cpf>42598727835</m:cpf>
  </m:consultaClienteRequest>
</soap:Body>
</soap:Envelope>

```

``` xml
RESPONSE - RESPOSTA:
HTTP/1.1 200 OK
Content-Type: application/soap+xml; charset=utf-8
Content-Length: nnn
 
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body xmlns:m="http://www.minhaempresa.com/consultaCliente">
  <m:consultaClienteResponse>
    <m:nome>James Paul McCartney</m:nome>
    <m:idade>75</m:idade>
    <m:profissao>Cantor</m:profissao>
    <m:datNascimento>1942-06-17T19:42:00</m:datNascimento>
  </m:consultaClienteResponse>
</soap:Body>
</soap:Envelope>
```