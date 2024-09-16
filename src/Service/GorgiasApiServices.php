<?php

namespace App\Service;


use GuzzleHttp\Client;

class GorgiasApiService
{

    private $client;

    public function __construct(string $gorgiasApiUrl, string $gorgiasApiKey)
    {
        $this->client = new Client([
            'base_uri' => $gorgiasApiUrl,
            'headers' => [
                'Authorization' => 'Basic ' . $gorgiasApiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getTickets($params)
    {
        $response = $this->client->get('/api/tickets', [
            'query' => $params,
        ]);

        return json_decode($response->getBody(), true);
    }
    public function getTicketsCustomField($id)
    {
        $response = $this->client->get('/api/tickets/' . $id );

        return json_decode($response->getBody(), true);
    }

    public function updateTicket($ticketId, $data)
    {
        $response = $this->client->put("/api/tickets/{$ticketId}", [
            'json' => $data,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function fetchIntegrations($params)
    {
        try {
            $response = $this->client->get('/api/integrations', [
                'query' => $params,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logger->error('Error fetching integrations: ' . $e->getMessage());
            return null;
        }
    }
    public function fetchStatistic($params)
    {
        try {
            $response = $this->client->get('/api/stats/overview', [
                'query' => $params,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logger->error('Error fetching integrations: ' . $e->getMessage());
            return null;
        }
    }
}
