<?php

namespace Conversorbinario\WebLogViewerBundle\Controller;

use Conversorbinario\WebLogViewerBundle\Model\LogList;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LogListController extends AbstractController
{

    private $kernelInstance;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernelInstance = $kernel;
    }
    /**
     * @Route("logs")
     */
    public function logListAction(Request $request)
    {
        $logDir = $this->kernelInstance->getLogDir();
        $logs = (new LogList())->getLogList($logDir);
        return $this->render('@WebLogViewer/listView.html.twig', [
            'logs' => $logs
        ]);
    }

}
