<?php

namespace Kendoctor\Bundle\DocumentorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Kendoctor\Bundle\DocumentorBundle\Entity\UserDocumentCategory;
use Kendoctor\Bundle\DocumentorBundle\Form\UserDocumentCategoryType;

/**
 * UserDocumentCategory controller.
 *
 * @Route("/userdocumentcategory")
 */
class UserDocumentCategoryController extends Controller
{
    /**
     * Lists all UserDocumentCategory entities.
     *
     * @Route("/", name="userdocumentcategory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KendoctorDocumentorBundle:UserDocumentCategory')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new UserDocumentCategory entity.
     *
     * @Route("/", name="userdocumentcategory_create")
     * @Method("POST")
     * @Template("KendoctorDocumentorBundle:UserDocumentCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new UserDocumentCategory();
        $form = $this->createForm(new UserDocumentCategoryType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('userdocumentcategory_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new UserDocumentCategory entity.
     *
     * @Route("/new", name="userdocumentcategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new UserDocumentCategory();
        $form   = $this->createForm(new UserDocumentCategoryType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a UserDocumentCategory entity.
     *
     * @Route("/{id}", name="userdocumentcategory_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KendoctorDocumentorBundle:UserDocumentCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserDocumentCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing UserDocumentCategory entity.
     *
     * @Route("/{id}/edit", name="userdocumentcategory_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KendoctorDocumentorBundle:UserDocumentCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserDocumentCategory entity.');
        }

        $editForm = $this->createForm(new UserDocumentCategoryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing UserDocumentCategory entity.
     *
     * @Route("/{id}", name="userdocumentcategory_update")
     * @Method("PUT")
     * @Template("KendoctorDocumentorBundle:UserDocumentCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KendoctorDocumentorBundle:UserDocumentCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserDocumentCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UserDocumentCategoryType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('userdocumentcategory_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a UserDocumentCategory entity.
     *
     * @Route("/{id}", name="userdocumentcategory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KendoctorDocumentorBundle:UserDocumentCategory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserDocumentCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('userdocumentcategory'));
    }

    /**
     * Creates a form to delete a UserDocumentCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
