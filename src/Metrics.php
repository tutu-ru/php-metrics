<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use TutuRu\Config\Config;
use TutuRu\Metrics\Exceptions\UnknownSessionException;
use TutuRu\Metrics\MetricsSession\MetricsSessionFactoryInterface;
use TutuRu\Metrics\MetricsSession\MetricsSessionInterface;

class Metrics implements MetricsInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var MetricsSessionInterface[] */
    private $sessions = [];

    /** @var MetricsSessionInterface */
    private $nullSession;

    /** @var MetricsConfig */
    private $config;

    /** @var MetricsSessionFactoryInterface */
    private $sessionFactory;


    public function __construct(
        Config $config,
        MetricsSessionFactoryInterface $sessionFactory,
        LoggerInterface $logger = null
    ) {
        $this->config = new MetricsConfig($config);
        $this->sessionFactory = $sessionFactory;
        if (!is_null($logger)) {
            $this->setLogger($logger);
        }
    }


    /**
     * @param string $name
     * @return MetricsSessionInterface
     * @throws UnknownSessionException
     */
    public function getSession(string $name): MetricsSessionInterface
    {
        if (!isset($this->sessions[$name])) {
            $sessionParameters = $this->config->getSessionParameters($name);
            if (is_null($sessionParameters)) {
                throw new UnknownSessionException("Unknown session '$name'");
            }
            $this->sessions[$name] = $this->createSession($sessionParameters);
        }
        return $this->sessions[$name];
    }


    public function getRequestedSessionOrDefault(string $name): MetricsSessionInterface
    {
        try {
            return $this->getSession($name);
        } catch (UnknownSessionException $e) {
            if (!is_null($this->logger)) {
                $this->logger->error("session {$name} is not defined. using default");
            }
            return $this->getDefaultSessionOrNull();
        }
    }


    private function getDefaultSessionOrNull()
    {
        $name = SessionNames::NAME_DEFAULT;
        try {
            return $this->getSession($name);
        } catch (UnknownSessionException $e) {
            if (!is_null($this->logger)) {
                $this->logger->error("session {$name} is not defined. using null");
            }
            return $this->getNullSession();
        }
    }


    public function getNullSession(): MetricsSessionInterface
    {
        if (is_null($this->nullSession)) {
            $this->nullSession = $this->sessionFactory->createNullSession();
        }
        return $this->nullSession;
    }


    protected function createSession(SessionParams $sessionParameters)
    {
        return $this->sessionFactory->createSession($sessionParameters, $this->config);
    }


    /**
     * @return MetricsSessionInterface[]
     */
    public function getAllSessions(): array
    {
        return $this->sessions;
    }


    public function send()
    {
        foreach ($this->getAllSessions() as $session) {
            $session->send();
        }
    }
}
