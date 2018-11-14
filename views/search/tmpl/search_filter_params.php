<div id="search-path">
	<?php if(isset($this->category)) { ?>
		<ul class="category-breadcrumbs">
			<li>
				<a class="search-filter-elem" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&resetSearch=1') ?>"><?php echo JText::_('LNG_ALL_CATEGORIES') ?></a>
			</li>
			<?php 
			if(isset($this->searchFilter["path"])) {
				foreach($this->searchFilter["path"] as $path) {
					if($path[0]==1)
						continue;
				?>
					<span class="divider">/</span>
					<li>
						<a class="search-filter-elem" href="<?php echo JBusinessUtil::getCategoryLink($path[0], $path[2]) ?>"><?php echo $path[1]?></a>
					</li>
				<?php } ?>
			<?php } ?>
			<span class="divider">/</span>
			<li>
				<?php if(!empty($this->category)) echo $this->category->name ?>
			</li>
		</ul>
	<?php } ?>
	<ul class="selected-criteria">
		<?php if(!empty($this->selectedParams["type"]) && !empty($this->searchFilter["types"])) {?>
			<li>
				<a class="filter-type-elem" onclick="removeFilterRule('type', <?php echo $this->selectedParams["type"][0] ?>)"><?php echo $this->searchFilter["types"][$this->selectedParams["type"][0]]->typeName; ?> x</a>
			</li>
		<?php $showClear++; } ?>

        <?php if(!empty($this->typeSearch) && empty($this->searchFilter["types"])) { ?>
            <li>
                <a class="filter-type-elem"
                   onclick="removeSearchRule('type')"><?php echo $this->typeSearchName; ?> x</a>
            </li>
            <?php $showClear++;
        } ?>

		<?php if(!empty($this->selectedParams["country"]) && !empty( $this->searchFilter["countries"])) {?>
			<li>
				<a  class="filter-type-elem" onclick="removeFilterRule('country', <?php echo $this->selectedParams["country"][0] ?>)"><?php echo $this->searchFilter["countries"][$this->selectedParams["country"][0]]->countryName; ?> x</a>
			</li>
		<?php $showClear++; }?>

        <?php if(!empty($this->countrySearch) && empty($this->searchFilter["countries"])) { ?>
            <li>
                <a class="filter-type-elem"
                   onclick="removeSearchRule('country')"><?php echo $this->country->country_name; ?> x</a>
            </li>
            <?php $showClear++;
        } ?>

		<?php if(!empty($this->selectedParams["region"]) && !empty( $this->searchFilter["regions"])) {?>
			<li>
				<a class="filter-type-elem" onclick="removeFilterRule('region', <?php echo "&quot;".$this->selectedParams["region"][0]."&quot;" ?>)"><?php echo $this->searchFilter["regions"][$this->selectedParams["region"][0]]->regionName; ?> x</a>
			</li>
		<?php $showClear++; } ?>

        <?php if(!empty($this->regionSearch) && empty($this->searchFilter["regions"])) { ?>
            <li>
                <a class="filter-type-elem"
                   onclick="removeSearchRule('region')"><?php echo $this->region->regionName; ?> x</a>
            </li>
            <?php $showClear++;
        } ?>

		<?php if(!empty($this->selectedParams["city"]) && !empty($this->searchFilter["cities"]) && isset( $this->searchFilter["cities"][$this->selectedParams["city"][0]])) {?>
			<li>
				<a class="filter-type-elem" onclick="removeFilterRule('city', <?php echo "&quot;".$this->selectedParams["city"][0]."&quot;" ?>)"><?php echo $this->searchFilter["cities"][$this->selectedParams["city"][0]]->cityName; ?> x</a>
			</li>
		<?php $showClear++; } ?>

        <?php if(!empty($this->citySearch) && empty($this->searchFilter["cities"])) { ?>
            <li>
                <a class="filter-type-elem"
                   onclick="removeSearchRule('city')"><?php echo $this->city->cityName; ?> x</a>
            </li>
            <?php $showClear++;
        } ?>

		<?php if(!empty($this->selectedParams["area"]) && !empty( $this->searchFilter["areas"])) {?>
			<li>
				<a class="filter-type-elem" class="remove" onclick="removeFilterRule('area', <?php echo "&quot;".$this->selectedParams["area"][0]."&quot;" ?>)"> <?php echo $this->searchFilter["areas"][$this->selectedParams["area"][0]]->areaName; ?> x</a>
			</li>
		<?php $showClear++; } ?>
        <?php if(!empty($this->selectedParams["province"]) && !empty($this->searchFilter["provinces"]) && isset( $this->searchFilter["provinces"][$this->selectedParams["province"][0]])) {?>
            <li>
                <a class="filter-type-elem" onclick="removeFilterRule('province', <?php echo "&quot;".$this->selectedParams["province"][0]."&quot;" ?>)"><?php echo $this->searchFilter["provinces"][$this->selectedParams["province"][0]]->provinceName; ?> x</a>
            </li>
        <?php $showClear++; } ?>

        <?php if(!empty($this->provinceSearch) && empty($this->searchFilter["provinces"])) { ?>
            <li>
                <a class="filter-type-elem"
                   onclick="removeSearchRule('province')"><?php echo $this->provinceSearch; ?> x</a>
            </li>
            <?php $showClear++;
        } ?>

        <?php if(!empty($this->searchkeyword)) { ?>
            <li>
                <a class="filter-type-elem"
                   onclick="removeSearchRule('keyword')"><?php echo $this->searchkeyword; ?> x</a>
            </li>
            <?php $showClear++;
        } ?>

        <?php if(!empty($this->customAtrributesValues)) {
            foreach ($this->customAtrributesValues as $attribute) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="removeAttrCond(<?php echo $attribute->attribute_id ?>)"><?php echo $attribute->name; ?> x </a>
                </li>
                <?php
                $showClear++;
            }
        } ?>

        <?php if(!empty($this->zipCode)) { ?>
            <li>
                <a class="filter-type-elem"
                   onclick="removeSearchRule('zipcode')"><?php echo $this->zipCode; ?> x </a>
            </li>
            <?php
            $showClear++;
        } ?>

		<?php if($showClear > 1) { ?>
			<span class="filter-type-elem reset"><a href="javascript:resetFilters(true)" style="text-decoration: none;"><?php echo JText::_('LNG_CLEAR_ALL'); ?></a></span>
		<?php } ?>
	</ul>
	<div class="clear"></div>
</div>