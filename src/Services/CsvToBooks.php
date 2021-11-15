<?php

namespace App\Services;

use App\Entity\Authors;
use App\Entity\Books;
use App\Entity\Categories;
use App\Repository\AuthorsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CsvToBooks
{
    private $entityManager;
    private $validator;
    private $authorRepository;
    private $categoryRepository;


    public function __construct( EntityManagerInterface $entityManager, ValidatorInterface $validator, AuthorsRepository $authorRepository, CategoriesRepository $categoryRepository)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->authorRepository = $authorRepository;
        $this->categoryRepository = $categoryRepository;
    }


    public function main (Reader $csvContent) : array
    {
        // Call method who gonna check each header of the csvContent
        $statusOfRequest = [];
        $count = 0;
        foreach ($csvContent as $row) {
            $count++;

            $books = (new Books())
                ->setTitle($row['title'])
                ->setDescription($row['description'])
                ->setParutedAt(new \DateTime($row['parutedAt']))
                ->setCover('images/image-default.jpg');

            $booksWithAuthor = $this->authors($books, $row['authors']);
            $booksWithCategory = $this->category($booksWithAuthor, $row['category']);

            $errors = $this->validator->validate($booksWithCategory);

            foreach ($errors as $error) {
                $statusOfRequest['errors'] = 'INSERTION STOPPÉ - Ligne ' . $count . ': ' . $error->getMessage();
                return $statusOfRequest;
            }
            $this->entityManager->persist($books);
        }

        $statusOfRequest['success'] = 'Insertion effectué avec succès.';
        $this->entityManager->flush();
        return $statusOfRequest;
    }


    public function authors (Books $books, string $authorRow) : Books
    {
        $explodeAuthors = explode(',', $authorRow);

        foreach($explodeAuthors as $author) {
            $newAuthorObj = (new Authors())->setName(trim($author));
            $authorFromDB = $this->authorRepository->findOneBy(['name' => $newAuthorObj->getName()]);

            if ($authorFromDB === null) {
                $this->entityManager->persist($newAuthorObj);
                $this->entityManager->flush();
                $books->addAuthor($newAuthorObj);
            } else {
                $books->addAuthor($authorFromDB);
            }
        }

        return $books;
    }


    public function category (Books $books, string $categoryRow) : Books
    {
        $explodeCategories = explode(',', $categoryRow);

        foreach($explodeCategories as $category) {
            $newCategoryObj = (new Categories())->setName(trim($category));
            $categoryFromDB = $this->categoryRepository->findOneBy(['name' => $newCategoryObj->getName()]);

            if ($categoryFromDB === null) {
                $this->entityManager->persist($newCategoryObj);
                $this->entityManager->flush();
                $books->addCategory($newCategoryObj);
            } else {
                $books->addCategory($categoryFromDB);
            }
        }

        return $books;
    }

}