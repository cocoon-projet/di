# Méthodes d'Injection

## Types d'Injection Supportés

### 1. Injection par Constructeur

```php
class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly LoggerInterface $logger
    ) {}
}
```

### 2. Injection par Méthode

```php
class NewsletterService
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function setMailer(MailerInterface $mailer): void
    {
        $this->mailer = $mailer;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}

// Configuration
$container->bind(NewsletterService::class, [
    '@class' => NewsletterService::class,
    '@methods' => [
        'setMailer' => [MailerInterface::class],
        'setLogger' => [LoggerInterface::class]
    ]
]);
```

## Bonnes Pratiques

1. Préférez l'injection par constructeur
2. Utilisez l'injection par méthode pour les dépendances optionnelles
3. Documentez les dépendances requises
4. Respectez le principe d'inversion des dépendances