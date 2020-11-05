# FACT-Finder Category Feed for OXID
Generate and upload a category feed for suggest data enrichment.

## How to use
Add this package to your available repositories:
    
    composer config -g repositories.ff-category-feed vcs https://github.com/fmarangi/oxid-ff-category-feed.git

Require it from your Oxid installation:

    composer require fmarangi/oxid-ff-category-feed
    
Export your categories:

    composer factfinder:export:categories --lang <langId> -- <shopID>
