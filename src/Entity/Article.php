<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\VirtualProperty;
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["article_list"])]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups(["article_list"])]
    #[Assert\NotBlank()]
    private $header;

    #[ORM\Column(type: 'text')]
    #[Groups(["article_list"])]
    #[Assert\NotBlank()]
    private $body;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["article_list"])]
    #[Assert\NotBlank()]
    private $writer;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["article_list"])]
    private $created_at;


    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["article_list"])]
    /**
     * @Gedmo\Slug(fields={"header"})
     */
    private $slug;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class,cascade:["persist"])]
    private $comments;

    #[ORM\ManyToOne(targetEntity: Media::class, inversedBy: 'articles')]
    #[Assert\NotBlank()]
    private $teaser_image;


    /**
     * @VirtualProperty
     * @SerializedName("num_comments")
     * @return int
     * @Groups ({"article_list"})
     */
    public function getNumberOfComments(){

        return count($this->getComments());
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getWriter(): ?User
    {
        return $this->writer;
    }

    public function setWriter(?User $writer): self
    {
        $this->writer = $writer;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtTime(): void
    {
        $this->created_at = new \DateTime("now");
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    public function getTeaserImage(): ?Media
    {
        return $this->teaser_image;
    }

    public function setTeaserImage(?Media $teaser_image): self
    {
        $this->teaser_image = $teaser_image;

        return $this;
    }


}
