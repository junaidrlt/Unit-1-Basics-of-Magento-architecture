<?php
declare(strict_types=1);

namespace RLTSquare\Training\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use RLTSquare\Training\Logger\Logger;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;

class Indexes implements ActionInterface
{
    protected $emailTemplate;
    protected $pageFactory;
    /**
     * @var Http
     */
    protected $request;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    private $logger;

    public function __construct(
        PageFactory $pageFactory,
        Logger $logger,
        Http $request,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface        $state,
        \Magento\Email\Model\BackendTemplate $emailTemplate
    ) {
        $this->pageFactory = $pageFactory;
        $this->logger = $logger;
        $this->request = $request;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->emailTemplate = $emailTemplate;
    }

    public function execute()
    {
        $this->sendEmail();
        $this->logger->info('Page Visited');
        $this->logger->info('Email Send');
        $pageFactory = $this->pageFactory->create();
        $pageFactory->getConfig()->getTitle()->set(__('Test'));
        return $pageFactory;
    }
    public function sendEmail()
    {
        $emails_template = $this->emailTemplate->load('email_template', 'orig_template_code');
        $templateId = 'email_template';
        $fromEmail = 'junaid.ashfaq@rltsquare.com';
        $fromName = 'junaid';
        $toEmail = 'junaid.ashfaq@rltsquare.com';

        try {
            $templateVars = [];

            $storeId = $this->storeManager->getStore()->getId();

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();

            $storeScope = ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($emails_template->getId())
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFromByScope($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}
