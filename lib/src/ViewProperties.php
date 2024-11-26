<?php

namespace app\src;

use app\assets\DB;

class ViewProperties
{
    private $ownerID;
    private $con;

    public function __construct()
    {
        $this->ownerID = $_SESSION['id'];
        $this->con = DB::getInstance();
    }


    /**
     * Get all properties for a particular property owner
     */
    public function showAdminProperties()
    {

        $houses = $this->con->select("id, title, index_img, price, summary, location, type, link", "properties", "WHERE owner_id = ? AND status = 'available' ORDER BY id DESC LIMIT 6", $this->ownerID);

        // Check if there is any available apartment
        if ($houses->num_rows < 1) : ?>
            <p class="text-rose-700 dark:text-rose-500 text-center lg:col-span-12 text-xl">
                You do not have any property yet. Use the <a class="text-sky-500 dark:text-sky-600 hover:underline hover:underline-offset-4 active:underline active:underline-offset-4" href="/admin/add-property"> Add New Property </a> button to get started.
            </p>
        <?php
            return;
        endif;

        while ($house = $houses->fetch_object()) : ?>
            <article class="lg:col-span-4 space-y-3 sm:col-span-6">
                <div class="relative">
                    <img class="property-listing-image" src="../assets/img/<?= $house->index_img ?>" alt="<?= $house->title ?>" title="<?= $house->title ?>" width="100%" height="200">

                    <i class="fr fi-rr-heart absolute top-2.5 right-4 text-2xl text-rose-500 dark:text-white"></i>
                </div>

                <div class="px-2 space-y-3">
                    <div class="flex items-center flex-wrap gap-x-4 gap-y-1.5 justify-between">
                        <span class=<?= $house->type === 'For Rent' ? "text-green-500 dark:text-green-400" : "text-rose-500 dark:text-rose-400" ?>>
                            <i class="fr <?= $house->type === 'For Rent' ? 'fi-rr-recycle' : 'fi-rr-thumbtack' ?>"></i>
                            <?= $house->type ?>
                        </span>

                        <span class="text-sky-500 lining-nums font-semibold tracking-widest">
                            ₱ <?= number_format($house->price) ?>
                        </span>
                    </div>

                    <div>
                        <h2 class="header">
                            <?= $house->title ?>
                        </h2>

                        <p>
                            <?= $house->summary ?>
                        </p>
                    </div>

                    <address>
                        <i class="fr fi-rr-map-marker-home"></i>
                        <?= $house->location ?>
                    </address>

                    <a class="inline-block rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" href="view-property?propertyID=<?= $house->id ?>&propertyName=<?= $house->link ?>">
                        View Details
                    </a>
                </div>
            </article>
<?php
        endwhile;
    }
}
