<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Template;
use App\Entity\Response;
use App\Entity\Answer;
use App\Form\TemplateType;
use App\Form\FillTemplateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

// #[Route('/template')]
final class TemplateController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index(): HttpResponse
    {
        $latestTemplates = $this->em->getRepository(Template::class)
            ->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(5) 
            ->getQuery()
            ->getResult();

        return $this->render('/main_page/index.html.twig', [
            'latestTemplates' => $latestTemplates,
        ]);
    }

   #[Route('/template/create', name: 'app_create_template', methods:['GET', 'POST'])] 
   public function create(Request $request):HttpResponse
   {
        $template = new Template();
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $tagNames = $form->get('tags')->getData();
            foreach ($tagNames as $tagName) {
                if (is_string($tagName)) {
                    $tag = $this->em->getRepository(Tag::class)->findOneBy(['name' => $tagName]);
                    if (!$tag) {
                        $tag = new Tag();
                        $tag->setName($tagName);
                        $this->em->persist($tag);
                    }
                    $template->addTag($tag);
                }
            } 
            $questionCounts = [
                'single_line' => 0,
                'multi_line' => 0,
                'positive_integer' => 0,
                'checkbox' => 0,
                'multiple_choice' => 0, 
                'dropdown' => 0,       
            ];

            
            foreach ($template->getQuestions() as $question) {
                $type = $question->getType();
                if (array_key_exists($type, $questionCounts)) {
                    $questionCounts[$type]++;
                    if ($questionCounts[$type] >= 4) {
                        $this->addFlash('error', "Cannot add more than 4 questions of type '$type'.");
                        return $this->render('template/create.html.twig', ['form' => $form->createView()]);
                    }
                } else {
                    $this->addFlash('error', "Invalid question type: '$type'.");
                    return $this->render('template/create.html.twig', ['form' => $form->createView()]);
                }
            }


            $this->em->persist($template);
            $this->em->flush();

            return $this->redirectToRoute('template_show', ['id' => $template->getId()]); 
        }

        return $this->render('template/create.html.twig', [
            'form' => $form->createView(),
        ]);
}

            #[Route('/template/{id}', name: 'template_show', methods: ['GET'])]
            public function show(Template $template): HttpResponse
            {
                return $this->render('template/show.html.twig', [
                    'template' => $template,
                ]);
            }

            #[Route('/template/{id}/fill', name: 'template_fill', methods: ['GET', 'POST'])]
            public function fill(Request $request, Template $template): HttpResponse
            {
                $form = $this->createForm(FillTemplateType::class, null, ['template' => $template]);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $response = new Response();
                    $response->setTemplate($template);
                    $response->setSubmittedAt(new \DateTime());

                    $data = $form->getData(); 
                    foreach ($template->getQuestions() as $question) {
                        $fieldName = 'question_' . $question->getId();
                        $answerText = $data[$fieldName] ?? '';

                        
                        if (in_array($question->getType(), ['multiple_choice', 'dropdown'])) {
                            
                            $answerText = is_string($answerText) ? trim($answerText) : '';
                        } elseif (is_bool($answerText)) {
                            $answerText = $answerText ? 'Yes' : 'No';
                        } else {
                            $answerText = (string) $answerText; 
                        }

                        $answer = new Answer();
                        $answer->setResponse($response);
                        $answer->setQuestion($question);
                        $answer->setAnswerText($answerText);
                        $this->em->persist($answer);
                    }

                    $this->em->persist($response);
                    $this->em->flush();

                    return $this->render('template/thanks.html.twig');
                }

                return $this->render('template/fill.html.twig', [
                    'template' => $template,
                    'form' => $form->createView(),
                ]);
            }


            #[Route('/template/{id}/responses', name: 'template_responses', methods: ['GET'])]
            public function responses(Template $template): HttpResponse
            {
                $responses = $this->em->getRepository(Response::class)
                    ->findBy(['template' => $template], ['submittedAt' => 'DESC']);
        
                
                $questionsToShow = array_filter($template->getQuestions()->toArray(), function ($question) {
                    return $question->isDisplayInTable();
                });
        
                return $this->render('template/response.html.twig', [
                    'template' => $template,
                    'responses' => $responses,
                    'questionsToShow' => $questionsToShow,
                ]);
            }

            #[Route('/responses/all', name: 'all_responses', methods: ['GET'])]
            public function allResponses(): HttpResponse
            {
                $responses = $this->em->getRepository(Response::class)
                    ->createQueryBuilder('r')
                    ->orderBy('r.submittedAt', 'DESC')
                    ->getQuery()
                    ->getResult();
        
                return $this->render('template/all_responses.html.twig', [
                    'responses' => $responses,
                ]);
            }
            
  
}