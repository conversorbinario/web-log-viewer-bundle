<?php

namespace Conversorbinario\WebLogViewerBundle\Controller;

use Conversorbinario\WebLogViewerBundle\Model\LogView;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LogViewerController extends AbstractController
{
    private $kernelInstance;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernelInstance = $kernel;
    }

    /**
     * @Route("logs/viewer", name="log_viewer")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logViewAction(Request $request)
    {
        $log = $request->query->get('log');
        $delete = filter_var($request->query->get('delete'), FILTER_VALIDATE_BOOLEAN);

        $logDir = $this->kernelInstance->getLogDir();
        $logfile = "$logDir/$log";

        // Check that the requested file is within the log directory:
        // we probe one character ahead to make sure we validate the full directory name
        // and not just directories that starts with the same substring
        $canonicalLogDir = realpath($logDir);
        $canonicalLogFile = realpath($logfile);
        if(substr($canonicalLogFile, 0, strlen($canonicalLogDir)+1) !== $canonicalLogDir.DIRECTORY_SEPARATOR){
            throw $this->createAccessDeniedException();
        }

        if($delete) {
            unlink($logfile);
            return $this->redirectToRoute('greenskies_weblogviewer_loglist_loglist');
        }

        if (file_exists($logfile)) {
            $log = file_get_contents($logfile);
            $context['log'] = LogView::logToArray($log);
        } else {
            $context['noLog'] = true;
        }
        return $this->render('@WebLogViewer/logView.html.twig', $context);
    }
}
