<?php

namespace EncryptBundle\Controller;

use EncryptBundle\Entity\EncryptFiles;
use EncryptBundle\Form\EncryptFileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class EncryptController extends Controller
{
    /**
     * @Route("/encrypt_file", name="encrypt")
     */
    public function encryptAction(Request $request)
    {
        $encryptFile = new EncryptFiles();
        $form = $this->createForm(EncryptFileType::class, $encryptFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $encryptFile->getFile();

            $fileName = $file->getClientOriginalName();

            $file->move(
                $this->getParameter('encrypt_directory'),
                $fileName
            );

            $path_parts = pathinfo($this->getParameter('encrypt_directory').'/'.$fileName);

            $encryptFile->setUser($this->getUser());
            $encryptFile->setFile($path_parts['filename']);
            $encryptFile->setFileExtension($path_parts['extension']);
            $encryptFile->setEncryptedFileName(md5(uniqid()).'.enc');

            exec('encrypt -encryption=encrypt -file="'. $path_parts['dirname'] .'/' . $encryptFile->getFile().'.'.$encryptFile->getFileExtension() . '" -output="' . $path_parts['dirname'] . '/' . $encryptFile->getEncryptedFileName() . '" -key=testtesttesttest');
            exec('rm "' . $path_parts['dirname'] .'/' . $encryptFile->getFile() . '.' . $encryptFile->getFileExtension() . '"');

            $em = $this->getDoctrine()->getManager();
            $em->persist($encryptFile);
            $em->flush();

            return $this->redirect($this->generateUrl('encrypted_files'));
        }
        return $this->render('EncryptBundle:Page:index.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Update File'
        ));
    }

    /**
     * @Route("/encrypt_file_download", name="encrypt_download")
     */
    public function encryptDownloadAction(Request $request)
    {
        $encryptFile = new EncryptFiles();
        $form = $this->createForm(EncryptFileType::class, $encryptFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $encryptFile->getFile();

            $fileName = $file->getClientOriginalName();

            $file->move(
                $this->getParameter('encrypt_directory'),
                $fileName
            );

            $path_parts = pathinfo($this->getParameter('encrypt_directory').'/'.$fileName);

            $encryptFile->setUser($this->getUser());
            $encryptFile->setFile($path_parts['filename']);
            $encryptFile->setFileExtension($path_parts['extension']);
            $encryptFile->setEncryptedFileName(md5(uniqid()).'.enc');

            exec('encrypt -encryption=encrypt -file="'. $path_parts['dirname'] .'/' . $encryptFile->getFile().'.'.$encryptFile->getFileExtension() . '" -output="' . $path_parts['dirname'] . '/' . $encryptFile->getFile() . '.enc" -key=testtesttesttest');

            $response = new BinaryFileResponse($this->getParameter('encrypt_directory') .'/' . $encryptFile->getFile() . '.enc');
            $response->trustXSendfileTypeHeader();
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $encryptFile->getFile() . '.enc',
                iconv('UTF-8', 'ASCII//TRANSLIT', $encryptFile->getFile() . '.enc')
            );
            $response->deleteFileAfterSend(true);

            exec('rm "' . $this->getParameter('encrypt_directory') .'/' . $encryptFile->getFile() . '.' . $encryptFile->getFileExtension() . '"');
            return $response;
        }
        return $this->render('EncryptBundle:Page:index.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Encrypt File'
        ));
    }

    /**
     * @Route("/decrypt_file_download", name="decrypt_download")
     */
    public function decryptDownloadAction(Request $request)
    {
        $encryptFile = new EncryptFiles();
        $form = $this->createForm(EncryptFileType::class, $encryptFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $encryptFile->getFile();

            $extension = $file->guessExtension();
            $fileName = $file->getClientOriginalName();

            $file->move(
                $this->getParameter('encrypt_directory'),
                $fileName
            );

            $path_parts = pathinfo($this->getParameter('encrypt_directory').'/'.$fileName);

            $encryptFile->setUser($this->getUser());
            $encryptFile->setFile($path_parts['filename']);
            $encryptFile->setFileExtension($path_parts['extension']);
            $encryptFile->setEncryptedFileName(md5(uniqid()).'.enc');

            exec('encrypt -encryption=decrypt -output="'. $path_parts['dirname'] .'/' . $encryptFile->getFile().'.'.$extension . '" -file="' . $path_parts['dirname'] . '/' . $encryptFile->getFile() . '.enc" -key=testtesttesttest');

            $response = new BinaryFileResponse($this->getParameter('encrypt_directory') .'/' . $encryptFile->getFile() . '.' . $extension);
            $response->trustXSendfileTypeHeader();
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $encryptFile->getFile() . '.' . $extension,
                iconv('UTF-8', 'ASCII//TRANSLIT', $encryptFile->getFile() . '.' . $extension)
            );
            $response->deleteFileAfterSend(true);

            exec('rm "' . $this->getParameter('encrypt_directory') .'/' . $encryptFile->getFile() . '.' . $encryptFile->getFileExtension() . '"');
            return $response;
        }
        return $this->render('EncryptBundle:Page:index.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Decrypt File'
        ));
    }


    /**
     * @Route("/encrypted_file", name="encrypted_files")
     */
    public function encryptedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $EncryptedFileList = $em->getRepository('EncryptBundle:EncryptFiles')->findBy(array('user' => $this->getUser()));
        return $this->render('EncryptBundle:Page:list.html.twig', array(
            'encrypted_files' => $EncryptedFileList,
        ));
    }

    /**
     * @Route("/download_file/{id}", name="download_files")
     */
    public function downloadAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $EncryptedFileList = $em->getRepository('EncryptBundle:EncryptFiles')->findBy(array('user' => $this->getUser(), 'id' => $id));
        $EncryptedFile = $EncryptedFileList[0];

        exec('encrypt -encryption=decrypt -output="'. $this->getParameter('encrypt_directory') .'/' . $EncryptedFile->getFile().'.'.$EncryptedFile->getFileExtension() . '" -file="' . $this->getParameter('encrypt_directory') . '/' . $EncryptedFile->getEncryptedFileName() . '" -key=testtesttesttest');
        $response = new BinaryFileResponse($this->getParameter('encrypt_directory') .'/' . $EncryptedFile->getFile() . '.' . $EncryptedFile->getFileExtension());
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $EncryptedFile->getFile() . '.' . $EncryptedFile->getFileExtension(),
            iconv('UTF-8', 'ASCII//TRANSLIT', $EncryptedFile->getFile() . '.' . $EncryptedFile->getFileExtension())
        );
        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * @Route("/encrypted_file_ajax", name="encrypted_files_ajax")
     */
    public function encryptedAjaxAction(Request $request)
    {
        $action = $request->get('action');
        $name = $request->get('name');
        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $EncryptedFileList = $em->getRepository('EncryptBundle:EncryptFiles')->findBy(array('user' => $this->getUser(), 'id' => $id));
        $EncryptedFile = $EncryptedFileList[0];
        if ($action == 'update') {
            $EncryptedFile->setFile($name);
            $em->flush();
        }
        if ($action == 'delete') {
            exec('rm "' . $this->getParameter('encrypt_directory') .'/' . $EncryptedFile->getEncryptedFileName() . '"');

            $em->remove($EncryptedFile);
            $em->flush();
        }
        if ($action == 'download') {
        }
        return new JsonResponse(array('success' =>'true'));
    }
}
