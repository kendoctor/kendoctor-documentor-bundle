<?php

namespace Kendoctor\Bundle\DocumentorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Kendoctor\Bundle\DocumentorBundle\Entity\Document;
use Kendoctor\Bundle\DocumentorBundle\Form\DocumentType;
use Kendoctor\Bundle\DocumentorBundle\Form\DocumentEditingType;
use Kendoctor\Bundle\DocumentorBundle\Form\DocumentVersionType;

/**
 * Document controller.
 *
 * @Route("/document")
 */
class DocumentController extends Controller {

    /**
     * Lists all Document entities.
     *
     * @Route("/", name="document")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KendoctorDocumentorBundle:Document')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Document entity.
     *
     * @Route("/", name="document_create")
     * @Method("POST")
     * @Template("KendoctorDocumentorBundle:Document:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Document();
        $form = $this->createForm(new DocumentType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $session = $this->container->get('session');
            $session->set('current_document_lang', $entity->getLocale());
            
            $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                    ->getVersionByDocumentHash($entity->getCurrentVersionHash());

            return $this->redirect($this->generateUrl('document_edit', array('versionId' => $version->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Document entity.
     *
     * @Route("/new", name="document_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Document();

        $entity->setLocale($this->getRequest()->getLocale());
        $form = $this->createForm(new DocumentType(), $entity);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("KendoctorDocumentorBundle:UserDocumentCategory");


        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<a href="#">' . $node['name'] . '</a>';
            }
        );

        $htmlTree = $repo->childrenHierarchy(
                null, /* starting from root nodes */ false, /* true: load all children, false: only direct */ $options
        );

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tree' => $htmlTree
        );
    }

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/{id}", name="document_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KendoctorDocumentorBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Document entity.
     *
     * @Route("/{versionId}/edit", name="document_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, $versionId) {


        $em = $this->getDoctrine()->getManager();

        $session = $this->container->get('session');
        $currentLang = $session->get('current_document_lang', $request->getLocale());

        $dql = "SELECT v, d, vl FROM 
            KendoctorDocumentorBundle:DocumentVersion v 
            JOIN v.document d
            LEFT JOIN d.versionLogs vl WITH vl.lang = :lang
            WHERE v.id = :id
            ";

        $version = $em->createQuery($dql)
                ->setParameter("id", $versionId)
                ->setParameter('lang', $currentLang)
                ->getSingleResult();

        
        $entity = $version->getDocument();
        $entity->setLocale($currentLang);

        $langs = $em->getRepository("KendoctorDocumentorBundle:Document")->getLanguagesOfDocument($entity->getId());

        // $repository = $em->getRepository('Gedmo\Translatable\Entity\Translation');
        // $translations = $repository->findTranslations($entity);
        // print_r($translations);
        //revert version info
        $entity->setTitle($version->getTitle());
        $entity->setBody($version->getContent());
        $entity->setCurrentVersionHash($version->getDocumentHash());


        //   $entity = $em->getRepository('KendoctorDocumentorBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        //   $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        //     $logs = $repo->getLogEntries($entity);
        $versionLogs = $entity->getVersionLogs();


        //pass logentry version to template

        $editForm = $this->createForm(new DocumentEditingType(), $entity);
        $deleteForm = $this->createDeleteForm($entity->getId());


        return array(
            'entity' => $entity,
            'version' => $version,
            'langs' => $langs,
            'currentLang' => $currentLang,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Document entity.
     *
     * @Route("/{id}", name="document_update")
     * @Method("PUT")
     * @Template("KendoctorDocumentorBundle:Document:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KendoctorDocumentorBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DocumentEditingType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            //   $entity->setLocale($request->getLocale());
            $em->persist($entity);
            $em->flush();

            $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                    ->getVersionByDocumentHash($entity->getCurrentVersionHash());

            return $this->redirect($this->generateUrl('document_edit', array('versionId' => $version->getId())));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Document entity.
     *
     * @Route("/{id}", name="document_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KendoctorDocumentorBundle:Document')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Document entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('document'));
    }

    /**
     * Creates a form to delete a Document entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * Deletes a Document entity.
     *
     * @Route("/release-version/{versionId}", name="document_release_version")
     * @Template()
     */
    public function releaseVersionAction(Request $request, $versionId) {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT v FROM 
            KendoctorDocumentorBundle:DocumentVersion v 
          
            WHERE v.id = :id
            ";

        $entity = $em->createQuery($dql)
                ->setParameter("id", $versionId)
                ->getSingleResult();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $form = $this->createForm(new DocumentVersionType(), $entity);



        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $entity->setIsReleased(true);
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('document_edit', array('versionId' => $entity->getId())));
            }
        }
        return array(
            'entity' => $entity,
            'versionNo' => $request->query->get('versionNo'),
            'form' => $form->createView()
        );
    }

    /**
     * Deletes a Document entity.
     *
     * @Route("/select-translate-language/{versionId}", name="document_select_translate_language")
     * @Template()
     */
    public function selectTranslateLanguageAction(Request $request, $versionId) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
                ->add('locale', 'language')
                ->getForm();

        $version = $em->getRepository('KendoctorDocumentorBundle:DocumentVersion')->find($versionId);
         //       getVersionOfDocumentWithLang($versionId, 'en');

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $session = $this->container->get('session');
                $session->set('current_document_lang', $data['locale']);
             
                $versionForLang = $em->getRepository('KendoctorDocumentorBundle:DocumentVersion')->getVersionOfDocumentForLang($version, $data['locale']);
                if($versionForLang == null)
                {
                    $document = $version->getDocument();
                    $document->setLocale($data['locale']);
                    $document->setVersion($version->getVersion());
                    $em->persist($document);
                    $em->flush();
                    
                    $versionForLang = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                        ->getVersionByDocumentHash($document->getCurrentVersionHash());
                }
                
               // \Doctrine\Common\Util\Debug::dump($versionForLang);
              
                return $this->redirect($this->generateUrl('document_edit', array('versionId' => $versionForLang->getId()
                )));
            }
        }
        //$query->setHint(Gedmo\TranslationListener::HINT_TRANSLATABLE_LOCALE, 'en');

        return array(
            'version' => $version,
            'form' => $form->createView()
        );
    }

}
