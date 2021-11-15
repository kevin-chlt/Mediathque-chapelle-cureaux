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
    private $statusOfRequest = [];
    private int $count = 0;


    public function __construct( EntityManagerInterface $entityManager, ValidatorInterface $validator, AuthorsRepository $authorRepository, CategoriesRepository $categoryRepository)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->authorRepository = $authorRepository;
        $this->categoryRepository = $categoryRepository;
    }

    // This method call all other method in this service for check the entities and return the state of the import
    public function main (Reader $csvContent) : array
    {
        if (!$this->checkHeader($csvContent)) {
            $this->statusOfRequest['errors'] = 'Les titres de vos colonnes sont invalides.';
            return $this->statusOfRequest;
        }

        foreach ($csvContent as $row) {
            $this->count++;

            // Check if the given date is valid
            if(!\DateTime::createFromFormat('d/m/Y', $row['Parution'])) {
                $this->statusOfRequest['errors'] = 'INSERTION STOPPÉ - Ligne ' . $this->count . ': La date de parution est au mauvais format, format autorisé: jj/mm/aaaa.';
                return $this->statusOfRequest;
            }

            $books = (new Books())
                ->setTitle($row['Titre'])
                ->setDescription($row['Description'])
                ->setParutedAt(\DateTime::createFromFormat('d/m/Y', $row['Parution']));

            // if an error, $books === null and error will be returned
            $booksWithAuthor = $this->authors($books, $row['Auteurs']);
            if($booksWithAuthor === null) {
                $this->statusOfRequest['errors'] = 'INSERTION STOPPÉ - Ligne ' . $this->count . ': Une erreur sur l\'auteur à été lévé.';
                return $this->statusOfRequest;
            }

            $booksWithCategory = $this->category($booksWithAuthor, $row['Categories']);
            if($booksWithCategory === null) {
                $this->statusOfRequest['errors'] = 'INSERTION STOPPÉ - Ligne ' . $this->count . ': Une erreur sur la catégorie à été lévé.';
                return $this->statusOfRequest;
            }

            //Check the final $books and persist him if is OK or return error to user.
            $errors = $this->validator->validate($booksWithCategory);
            foreach ($errors as $error) {
                $this->statusOfRequest['errors'] = 'INSERTION STOPPÉ - Ligne ' . $this->count . ': ' . $error->getMessage();
                return $this->statusOfRequest;
            }
            $this->entityManager->persist($books);
        }

        $this->statusOfRequest['success'] = 'Insertion effectué avec succès.';
        $this->entityManager->flush();
        return $this->statusOfRequest;
    }

    // Check each authors in each row and persist him if he isn't in DB or return null if an error occurred.
    private function authors (Books $books, string $authorRow) : ?Books
    {
        $explodeAuthors = explode(',', $authorRow);

        foreach($explodeAuthors as $author) {
            $newAuthorObj = (new Authors())->setName(trim($author));
            $authorFromDB = $this->authorRepository->findOneBy(['name' => $newAuthorObj->getName()]);

            if ($authorFromDB === null) {
                $errors = $this->validator->validate($newAuthorObj);

                foreach ($errors as $ignored) {
                    return null;
                }

                $this->entityManager->persist($newAuthorObj);
                $this->entityManager->flush();
                $books->addAuthor($newAuthorObj);
            } else {
                $books->addAuthor($authorFromDB);
            }
        }

        return $books;
    }

    // Same as authors but for categories of the books
    private function category (Books $books, string $categoryRow) : ?Books
    {
        $explodeCategories = explode(',', $categoryRow);

        foreach($explodeCategories as $category) {
            $newCategoryObj = (new Categories())->setName(trim($category));
            $categoryFromDB = $this->categoryRepository->findOneBy(['name' => $newCategoryObj->getName()]);

            if ($categoryFromDB === null) {
                $errors = $this->validator->validate($newCategoryObj);

                foreach ($errors as $ignored) {
                    return null;
                }

                $this->entityManager->persist($newCategoryObj);
                $this->entityManager->flush();
                $books->addCategory($newCategoryObj);
            } else {
                $books->addCategory($categoryFromDB);
            }
        }

        return $books;
    }

    // Check the first line and return error if a line isn't exist in array headerAccepted.
    private function checkHeader (Reader $csvContent) : bool
    {
        $headersAccepted = ['Titre', 'Parution', 'Description', 'Auteurs', 'Categories'];
        foreach ($csvContent->getHeader() as $header) {
            if(!in_array($header, $headersAccepted)) {
                return false;
            }
        }

        return true;
    }

}