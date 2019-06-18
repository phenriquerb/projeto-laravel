<?php
declare(strict_types=1);//serve pra setar o que a funcao tem que retornar
use CodeShopping\Models\Product;
use CodeShopping\Models\ProductPhoto;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ProductPhotosTableSeeder extends Seeder
{
    /**\
     * @var Collection
    */
    private $allFakerPhotos;
    private $fakerPhotosPath = 'app/faker/product_photos';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->allFakerPhotos = $this->getFakerPhotos();
        /** @var Product $products */
        $products = Product::all();
        $this->deleteAllPhotosInProductsPath();
        $self = $this;
        $products->each(function($product) use ($self){
            $self->createPhotoDir($product);
            $self->createPhotosModels($product);
        });
    }

    private function getFakerPhotos(): Collection
    {
        $path = storage_path($this->fakerPhotosPath);
        return collect(\File::allFiles($path));
    }

    private function deleteAllPhotosInProductsPath()
    {
        $path = ProductPhoto::PRODUCTS_PATH;
        \File::deleteDirectory(storage_path($path), true); //O true no ultimo parametro é para n excluir o proprio diretorio
    }

    private function createPhotoDir(Product $product)
    {
        $path = ProductPhoto::photosPath($product->id);
        \File::makeDirectory($path, 0777, true); //0777 é o modo da pasta q poderar ser criada e o true é para abilitar a criaçao de pasta de forma recursiva caso ela n exista
    }

    private function createPhotosModels(Product $product)
    {
        foreach (range(1,3) as $v){
            $this->createProtoModel($product);
        }
    }

    private function createProtoModel(Product $product)
    {
        $photo = ProductPhoto::create([
            'product_id' => $product->id,
            'file_name' => 'imagem.png'
        ]);
        $this->generateProto($photo);
    }

    private function generateProto(ProductPhoto $photo)
    {
        $photo->file_name = $this->uploadPhoto($photo->product_id);
        $photo->save();
    }

    private function uploadPhoto($productId): string
    {
        /** @var SplFileInfo $photoFile */
        $photoFile = $this->allFakerPhotos->random();
        $uploadFile = new UploadedFile(
          $photoFile->getRealPath(),
          str_random(16) . '.' . $photoFile->getExtension()
        );
        //upload da photo
        ProductPhoto::uploadFiles($productId,[$uploadFile]);
        return $uploadFile->hashName();
    }
}
