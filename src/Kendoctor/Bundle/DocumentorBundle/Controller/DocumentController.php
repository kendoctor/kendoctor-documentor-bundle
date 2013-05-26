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
use Kendoctor\Bundle\DocumentorBundle\Entity\DocumentVersion;

/**
 * Document controller.
 *
 * @Route("/document")
 */
class DocumentController extends Controller {

    public function logNewVersion(Document $document, $em, DocumentVersion $oldVersion = null, $forceReleased = false) {
        $version = new DocumentVersion();
        $version->setDocument($document);
        $version->setContent($document->getBody());
        $version->setLang($document->getLocale());
        $version->setTitle($document->getTitle());
        $version->setCreatedAt(new \DateTime());

        if ($oldVersion && $oldVersion->getIsReleased()) {
            $versionNo = $version->getVersion();
            $version->setVersion($oldVersion->getVersion());
            $version->setIsReleased(true);
            $oldVersion->setIsReleased(false);
            $oldVersion->setVersion($versionNo);

            $em->persist($oldVersion);
        } else {
            $version->setIsReleased($forceReleased);
            if ($document->getVersion()) {
                $version->setVersion($document->getVersion());
            }
        }

        $em->persist($version);
        $em->flush();

        return $version;
    }

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
            //set current document language
            $session->set('current_document_lang', $entity->getLocale());

            //log a new version for document
            $version = $this->logNewVersion($entity, $em);

            return $this->redirect($this->generateUrl('document_edit', array('id' => $entity->getId())));
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
     * @Route("/{id}-{version}/edit", name="document_edit", defaults={"version"="latest"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, $id, $version) {


        $em = $this->getDoctrine()->getManager();

        $session = $this->container->get('session');
        $currentLang = $session->get('current_document_lang', $request->getLocale());

        if ($version == "latest") {
            $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                    ->getLatestByLangAndDocumentId($id, $currentLang);
            $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                    ->getById($version->getId());
        } else {
            $version = $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                    ->getById($version);
        }

        if ($version === null) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }
        // if version got by id need reset lang
        $currentLang = $version->getLang();
        $session->set('current_document_lang', $currentLang);

        $entity = $version->getDocumentInVersion();

        $langs = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")->getAvailableLangsOfDocument($entity->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }


        //pass logentry version to template

        $editForm = $this->createForm(new DocumentEditingType(), $entity);
        $deleteForm = $this->createDeleteForm($entity->getId());


        return array(
            'entity' => $entity,
            'version' => $version,
            'langs' => $langs,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Document entity.
     *
     * @Route("/{id}-{version}", name="document_update")
     * @Method("PUT")
     * @Template("KendoctorDocumentorBundle:Document:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $version) {
        $em = $this->getDoctrine()->getManager();

        $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                ->getById($version);

        if (!$version) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $entity = $version->getDocumentInVersion();

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DocumentEditingType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            //   $entity->setLocale($request->getLocale());
            $em->persist($entity);
            $em->flush();

            $newVersion = $this->logNewVersion($entity, $em, $version);

            return $this->redirect($this->generateUrl('document_edit', array('id' => $entity->getId(), 'version' => $newVersion->getId())));
        }

        $langs = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")->getAvailableLangsOfDocument($entity->getId());

        return array(
            'entity' => $entity,
            'version' => $version,
            'langs' => $langs,
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

        $entity = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                ->getById($versionId);

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
                return $this->redirect($this->generateUrl('document_edit', array('id' => $entity->getDocument()->getId(), 'version' => $entity->getId())));
            }
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * If the selected language version does not exisist, create it
     * 
     * @param type $versionId
     * @Route("/change-version-language/{versionId}-{lang}/", name="document_change_version_language")
     */
    public function changeVersionLanguageAction(Request $request, $lang, $versionId) {
        $em = $this->getDoctrine()->getManager();
        $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                ->getById($versionId);
        if (!$version) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $em = $this->getDoctrine()->getManager();
        $theSameVersion = $em->getRepository('KendoctorDocumentorBundle:DocumentVersion')->getTheSameVersionForLang($version, $lang);
        if ($theSameVersion == null) {
            $document = $version->getDocumentInVersion();
            $document->setLocale($lang);
            $document->setVersion($version->getVersion());
            $em->persist($document);
            $em->flush();
            $theSameVersion = $this->logNewVersion($document, $em, null, $version->getIsReleased());
        }
        $session = $this->container->get('session');
        $session->set('current_document_lang', $lang);
        return $this->redirect($this->generateUrl('document_edit', array(
                            'id' => $theSameVersion->getDocument()->getId(),
                            'version' => $theSameVersion->getId()
        )));
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

        $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                ->getById($versionId);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($data['locale'] == $version->getLang()) {
                    return $this->redirect($this->generateUrl('document_edit', array(
                                        'id' => $version->getDocument()->getId(),
                                        'version' => $version->getId()
                    )));
                }
                $session = $this->container->get('session');
                $session->set('current_document_lang', $data['locale']);

                $theSameVersion = $em->getRepository('KendoctorDocumentorBundle:DocumentVersion')->getTheSameVersionForLang($version, $data['locale']);
                if ($theSameVersion == null) {
                    $document = $version->getDocumentInVersion();
                    $document->setLocale($data['locale']);
                    $document->setVersion($version->getVersion());
                    $em->persist($document);
                    $em->flush();
                    $theSameVersion = $this->logNewVersion($document, $em, null, $version->getIsReleased());
                }

                // \Doctrine\Common\Util\Debug::dump($versionForLang);

                return $this->redirect($this->generateUrl('document_edit', array(
                                    'id' => $theSameVersion->getDocument()->getId(),
                                    'version' => $theSameVersion->getId()
                )));
            }
        }
        //$query->setHint(Gedmo\TranslationListener::HINT_TRANSLATABLE_LOCALE, 'en');

        return array(
            'version' => $version,
            'form' => $form->createView()
        );
    }

    
    /**
     * Deletes a Document version entity.
     *
     * @Route("/delete-version/{versionId}", name="document_delete_version")
     * @Template()
     */
    public function deleteVersionAction($versionId) {
        $em = $this->getDoctrine()->getManager();
        $version = $em->getRepository("KendoctorDocumentorBundle:DocumentVersion")
                ->getById($versionId);
        if (!$version) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }
        $document = $version->getDocumentInVersion();
        $em->remove($version);
        $em->flush();
        return $this->redirect($this->generateUrl('document_edit', array(
                            'id' => $document->getId()
        )));
    }

}
