<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ {
    ImageRepository, CategoryRepository, AlbumRepository, NotificationRepository
};
use App\Models\User;
use App\Models\Image;
use App\Notifications\ImageRated;

class ImageController extends Controller
{
    /**
     * Image repository.
     *
     * @var \App\Repositories\ImageRepository
     */
    protected $imageRepository;

    /**
     * Category repository.
     *
     * @var \App\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Album repository.
     *
     * @var \App\Repositories\AlbumRepository
     */
    protected $albumRepository;


    /**
     * Create a new ImageController instance.
     *
     * @param  \App\Repositories\ImageRepository $imageRepository
     * @param  \App\Repositories\CategoryRepository $categoryRepository
     */
    public function __construct(
        ImageRepository $imageRepository,
        CategoryRepository $categoryRepository,
        AlbumRepository $albumRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->categoryRepository = $categoryRepository;
        $this->albumRepository = $albumRepository;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view ('images.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate ([
            'image' => 'required|image|max:2000',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:255',
        ]);

        $this->imageRepository->store ($request);

        return back ()->with ('ok', __ ("L'image a bien été enregistrée"));
    }

    public function destroy(Image $image)
    {
        $this->authorize ('manage', $image);
        $image->delete ();
        return back ();
    }

    public function update(Request $request, Image $image)
    {
        $this->authorize('manage', $image);
        $image->category_id = $request->category_id;
        $image->save();
        return back()->with('updated', __('La catégorie a bien été changée !'));
    }

    public function adultUpdate(Request $request, Image $image)
    {
        $this->authorize('manage', $image);
        $image->adult = $request->adult == 'true';
        $image->save();
        return response ()->json();
    }
    /**
     * Display a listing of the images for the specified category.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function category($slug)
    {
        $category = $this->categoryRepository->getBySlug ($slug);
        $images = $this->imageRepository->getImagesForCategory ($slug);

        return view ('home', compact ('category', 'images'));
    }

    public function user(User $user)
    {
        $images = $this->imageRepository->getImagesForUser ($user->id);
        return view ('home', compact ('user', 'images'));
    }

    public function descriptionUpdate(Request $request, Image $image)
    {
        $this->authorize ('manage', $image);
        $request->validate ([
            'description' => 'nullable|string|max:255'
        ]);
        $image->description = $request->description;
        $image->save();
        return $image;
    }

    public function album($slug)
    {
        $album = $this->albumRepository->getBySlug ($slug);
        $images = $this->imageRepository->getImagesForAlbum ($slug);
        return view ('home', compact ('album', 'images'));
    }

    public function albums(Request $request, Image $image)
    {
        $this->authorize ('manage', $image);
        $albums = $this->albumRepository->getAlbumsWithImages ($request->user ());
        return view ('images.albums', compact('albums', 'image'));
    }

    public function albumsUpdate(Request $request, Image $image)
    {
        $this->authorize ('manage', $image);
        
        $image->albums()->sync($request->albums);
        $path = pathinfo (parse_url(url()->previous())['path']);
        if($path['dirname'] === '/album') {
            $album = $this->albumRepository->getBySlug ($path['basename']);
            if($this->imageRepository->isNotInAlbum ($image, $album)) {
                return response ()->json('reload');
            }
        }
        return response ()->json();
    }

    public function rate(Request $request, Image $image)
    {
        $user = $request->user();

        // Is user image owner ?
        if($this->imageRepository->isOwner($user, $image)){
            return response()->json(['status' => 'no']);
        }

        //rating
        $rate = $this->imageRepository->rateImage ($user, $image, $request->value);
        $this->imageRepository->setImageRate($image);

        // Notification
        $notificationRepository->deleteDuplicate($user, $image);
        $image->user->notify(new ImageRated($image, $request->value, $user->id));
        
        return [
            'status' => 'ok',
            'id' => $image->id,
            'value' => $image->rate,
            'count' => $image->users->count(),
            'rate' => $rate
        ];
    }

    public function click(Request $request, Image $image)
    {
        if ($request->session()->has('images') && in_array ($image->id, session ('images'))) {
            return response ()->json (['increment' => false]);
        }
        $request->session()->push('images', $image->id);
        $image->increment('clicks');
        return ['increment' => true];
    }
}
