<?php

namespace App\Repositories;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

class ImageRepository
{
    /**
     * Store image.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function store($request)
    {
        // Save image
        $path = basename ($request->image->store('images'));

        // Save thumb
        $image = InterventionImage::make ($request->image)->widen (500)->encode ();
        Storage::put ('thumbs/' . $path, $image);

        // Save in base
        $image = new Image;
        $image->description = $request->description;
        $image->category_id = $request->category_id;
        $image->adult = isset($request->adult);
        $image->name = $path;
        $request->user()->images()->save($image);
    }

    /**
     * Get all images.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllImages()
    {
        return $this->paginateAndRate (Image::latestWithUser());
    }

    /**
     * Get images for category.
     *
     * @param  string $slug
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getImagesForCategory($slug)
    {
        $query = Image::latestWithUser()->whereHas('category', function ($query) use ($slug) {
            $query->whereSlug($slug);
        });
        return $this->paginateAndRate ($query);
    }

    public function getImagesForUser($id)
    {
        $query = Image::latestWithUser ()->whereHas ('user', function ($query) use ($id) {
            $query->whereId($id);
        });
        return $this->paginateAndRate ($query);
    }

    public function getImagesForAlbum($slug)
    {
        $query = Image::latestWithUser ()->whereHas ('albums', function ($query) use ($slug) {
            $query->whereSlug ($slug);
        });
        return $this->paginateAndRate ($query);
    }

    public function isNotInAlbum($image, $album)
    {
        return $image->albums()->where('albums.id', $album->id)->doesntExist();
    }

    public function getOrphans()
    {
        return collect (Storage::files ('images'))->transform(function ($item) {
            return basename($item);
        })->diff (Image::select ('name')->pluck ('name'));
    }

    public function destroyOrphans()
    {
        $orphans = $this->getOrphans();
        foreach ($orphans as $orphan){
            Storage::delete ([
                'images/' . $orphan,
                'thumbs/' . $orphan,
            ]);
        }
    }

    public function rateImage($user, $imagen, $value)
    {
        $rate = $image->users()->where('users.id', $user->id)->pluck('rating')->first();

        if($rate) {
            if($rate !== $value){
                $image->users()->updateExistingPivot($user->id, ['rating' => $value]);
            }
        } else {
            $image->users ()-> attach ($user->id, ['rating' =>value]);
        }

        return $rate;
    }

    public function isOwner($user, $image)
    {
        return $image->user()->where('users.id', $user->id)->exists();
    }

    public function paginateAndRate($query)
    {
        $images = $query->paginate (config ('app.pagination'));
        return $this->setRating($images);
    }

    public function setRating($images)
    {
        $images->transform(function ($image) {
            $this->setImageRate ($image);
            return $image;
        });

        return $images;
    }

    public function setImageRate($image)
    {
        $number = $image->users->count();
        $image->rate = $number ? $image->users->pluck ('pivot.rating')->sum () / $number : 0;
    }
}
